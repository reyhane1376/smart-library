<?php

namespace App\Services;

use App\Interfaces\BorrowingServiceInterface;
use App\Interfaces\EventStoreInterface;
use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\ReservationServiceInterface;
use App\Interfaces\ScoreServiceInterface;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowingService implements BorrowingServiceInterface
{
    protected $notificationService;
    protected $scoreService;
    protected $reservationService;
    protected $eventStore;

    public function __construct(
        NotificationServiceInterface $notificationService,
        ScoreServiceInterface $scoreService,
        ReservationServiceInterface $reservationService,
        EventStoreInterface $eventStore
    ) {
        $this->notificationService = $notificationService;
        $this->scoreService = $scoreService;
        $this->reservationService = $reservationService;
        $this->eventStore = $eventStore;
    }

    public function borrowBook(User $user, BookCopy $copy)
    {
        return DB::transaction(function () use ($user, $copy) {
            // Check if user has active reservation for this copy
            $hasReservation = Reservation::where('user_id', $user->id)
                ->where('book_copy_id', $copy->id)
                ->where('status', Reservation::STATUS_ACTIVE)
                ->exists();


            // If copy is available or user has reservation
            if ($copy->status === BookCopy::STATUS_AVAILABLE || ($copy->status === BookCopy::STATUS_RESERVED && $hasReservation)) {
                // Create borrowing record
                $borrowingPeriod = $user->is_vip ? USER::BORROW_PERIOD_VIP_USER : USER::BORROW_PERIOD_USER ; // VIP users get longer borrowing period
                
                $borrowing = Borrowing::create([
                    'user_id'      => $user->id,
                    'book_copy_id' => $copy->id,
                    'borrowed_at'  => now(),
                    'due_date'     => now()->addDays($borrowingPeriod)
                ]);

                // Update copy status
                $copy->status = BookCopy::STATUS_BORROWED;
                $copy->save();

                // If there was a reservation, mark it as completed
                if ($hasReservation) {
                    Reservation::where('user_id', $user->id)
                        ->where('book_copy_id', $copy->id)
                        ->where('status', Reservation::STATUS_ACTIVE)
                        ->update(['status' => Reservation::STATUS_COMPLETED]);
                }

                // Store event
                $this->eventStore->storeEvent(
                    Event::EVENT_TYPE_BOOK_BORROWED,
                    Event::AGGREGATE_TYPE_BORROWING,
                    $borrowing->id,
                    [
                        'user_id' => $user->id,
                        'book_copy_id' => $copy->id,
                        'due_date' => $borrowing->due_date->toDateString()
                    ]
                );

                return $borrowing;
            }

            throw new \Exception('کتاب مورد نظر برای امانت موجود نمیباشد.');
        });
    }

    public function returnBook(Borrowing $borrowing, $condition)
    {
        return DB::transaction(function () use ($borrowing, $condition) {
            $now = now();
            $copy = $borrowing->bookCopy;
            $user = $borrowing->user;

            // Update borrowing record
            $borrowing->returned_at = $now;
            $borrowing->return_condition = $condition;
            
            // Calculate delay days
            if ($now->gt($borrowing->due_date)) {
                $borrowing->delay_days = $now->diffInDays($borrowing->due_date);
            
                // Calculate fine
                $borrowing->fine_amount = $this->calculateFine($borrowing);
                
                // Update user score for delay
                $this->scoreService->updateScoreForDelay($user, $borrowing->delay_days);
            }
            
            $borrowing->save();

            // Update copy status and condition if damaged
            $copy->status = BookCopy::STATUS_AVAILABLE;
            if ($condition === BookCopy::CONDITION_NEEDS_REPAIR) {
                $copy->physical_condition = BookCopy::CONDITION_NEEDS_REPAIR;
                
                // Update user score for damage
                $this->scoreService->updateScoreForDamage($user, BookCopy::CONDITION_NEEDS_REPAIR);
            }
            $copy->save();

            // Handle chain reservation
            $this->reservationService->handleChainedReservation($copy);
            dd('hi');

            // Store event
            $this->eventStore->storeEvent(
                Event::EVENT_TYPE_BOOK_RETURNED,
                Event::AGGREGATE_TYPE_BORROWING,
                $borrowing->id,
                [
                    'returned_at' => $now->toDateTimeString(),
                    'condition'   => $condition,
                    'delay_days'  => $borrowing->delay_days,
                    'fine_amount' => $borrowing->fine_amount
                ]
            );

            return $borrowing;
        });
    }

    public function calculateFine(Borrowing $borrowing)
    {
        $user = $borrowing->user;
        $delayDays = $borrowing->delay_days;
        
        // Base fine rate per day
        $baseFineRate = User::FINE_FOR_ONE_DAY; // 
        
        // Multiplier based on user history
        $previousDelays = Borrowing::where('user_id', $user->id)
            ->where('delay_days', '>', 0)
            ->where('id', '!=', $borrowing->id)
            ->count();
        
        // Progressive penalty: increase rate for repeat offenders
        $multiplier = 1.0;
        if ($previousDelays > 0) {
            $multiplier = min(1.0 + ($previousDelays * 0.1), 2.0); // Max double the fine
        }
        
        // Calculate fine amount
        $fineAmount = $baseFineRate * $delayDays * $multiplier;
        
        // Premium/rare book additional fine
        if ($borrowing->bookCopy->is_premium) {
            $fineAmount *= 1.5;
        }
        
        return round($fineAmount, 2);
    }

    public function extendBorrowingPeriod(Borrowing $borrowing, $days)
    {
        if ($borrowing->returned_at) {
            throw new \Exception('Cannot extend period for returned books');
        }
        
        // Check if user is eligible for extension
        $user = $borrowing->user;
        $eligibility = $this->scoreService->getUserEligibility($user);
        
        if ($eligibility < 70) { // Require good standing for extensions
            throw new \Exception('User is not eligible for borrowing extension');
        }
        
        // Check if book has pending reservations
        $hasPendingReservations = Reservation::where('book_copy_id', $borrowing->book_copy_id)
            ->where('status', 'pending')
            ->exists();
            
        if ($hasPendingReservations) {
            throw new \Exception('Cannot extend borrowing period when others are waiting');
        }
        
        // Update due date
        $maxExtensionDays = $user->is_vip ? 14 : 7;
        $extendDays = min($days, $maxExtensionDays);
        
        $borrowing->due_date = Carbon::parse($borrowing->due_date)->addDays($extendDays);
        $borrowing->save();
        
        // Store event
        $this->eventStore->storeEvent(
            'borrowing_extended',
            'borrowing',
            $borrowing->id,
            [
                'previous_due_date' => Carbon::parse($borrowing->due_date)->subDays($extendDays)->toDateString(),
                'new_due_date' => $borrowing->due_date->toDateString(),
                'extended_days' => $extendDays
            ]
        );
        
        return $borrowing;
    }
}