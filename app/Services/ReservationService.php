<?php

namespace App\Services;

use App\Interfaces\EventStoreInterface;
use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\QueueServiceInterface;
use App\Interfaces\ReservationServiceInterface;
use App\Interfaces\ScoreServiceInterface;
use App\Models\BookCopy;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReservationService implements ReservationServiceInterface
{
    protected $notificationService;
    protected $scoreService;
    protected $eventStore;
    protected $queueService;

    public function __construct(
        NotificationServiceInterface $notificationService,
        ScoreServiceInterface $scoreService,
        EventStoreInterface $eventStore,
        QueueServiceInterface $queueService
    ) {
        $this->notificationService = $notificationService;
        $this->scoreService = $scoreService;
        $this->eventStore = $eventStore;
        $this->queueService = $queueService;
    }

    public function reserveBook(User $user, BookCopy $copy)
    {
        // Check user eligibility
        if (!$this->isUserEligibleForReservation($user, $copy)) {
            throw new \Exception('User is not eligible for reservation');
        }

        // Start transaction for Optimistic Locking
        return DB::transaction(function () use ($user, $copy) {
            // Get the latest version
            $latestReservation = Reservation::where('book_copy_id', $copy->id)
                ->where('status', Reservation::STATUS_ACTIVE)
                ->first();

            // Check if book is available for immediate reservation
            if ($copy->status === BookCopy::STATUS_AVAILABLE && !$latestReservation) {

                $affectedRows = BookCopy::where('id', $copy->id)
                ->where('version', $copy->version) 
                ->update([
                    'status' => BookCopy::STATUS_RESERVED,
                    'version' => $copy->version + 1 
                ]);
    
                if ($affectedRows === 0) {
                    throw new \Exception('کتاب مورد نظر توسط فرد دیگری در حالا رزرو است.');
                }

                $reservation = $this->createReservation($user, $copy, 0, Reservation::STATUS_ACTIVE);

            } else {
                // Calculate queue position
                $queuePosition = Reservation::where('book_copy_id', $copy->id)
                    ->where('status', Reservation::STATUS_PENDING)
                    ->max('queue_position') + 1;

                $reservation = $this->createReservation($user, $copy, $queuePosition, Reservation::STATUS_PENDING);
            }

            // Store event
            $this->eventStore->storeEvent(
                Event::EVENT_TYPE_RESERVATION_CREATED,
                Event::AGGREGATE_TYPE_RESERVATION,
                $reservation->id,
                [
                    'user_id' => $user->id,
                    'book_copy_id' => $copy->id,
                    'queue_position' => $reservation->queue_position
                ]
            );

            // Invalidate cache
            Cache::tags(['reservations', "user_{$user->id}", "copy_{$copy->id}"])->flush();

            return $reservation;
        }, 5); // Retry 5 times in case of deadlock
    }

    protected function createReservation(User $user, BookCopy $copy, $queuePosition, $status)
    {
        return Reservation::create([
            'user_id'        => $user->id,
            'book_copy_id'   => $copy->id,
            'reserved_at'    => now(),
            'expires_at'     => now()->addDays(3),   // Reservation expires in 3 days
            'status'         => $status,
            'queue_position' => $queuePosition,
            'version'        => 0
        ]);
    }

    public function cancelReservation(Reservation $reservation)
    {
        return DB::transaction(function () use ($reservation) {
            // Optimistic locking check
            $currentVersion = $reservation->version;
            $updated = Reservation::where('id', $reservation->id)
                ->where('version', $currentVersion)
                ->update([
                    'status' => Reservation::STATUS_CANCELLED,
                    'version' => $currentVersion + 1
                ]);

            if (!$updated) {
                throw new \Exception('کتاب مورد نظر توسط فرد دیگری در حالا رزرو است.');
            }

            $reservation->refresh();

            // If this was an active reservation, make the copy available
            if ($reservation->status === Reservation::STATUS_ACTIVE) {
                $copy = $reservation->bookCopy;
                $copy->status = BookCopy::STATUS_AVAILABLE;
                $copy->save();

                // Notify next in queue
                $this->handleChainedReservation($copy);
            }

            // Update user score for cancellation
            $this->scoreService->updateScoreForCancelledReservations($reservation->user);

            // Store event
            $this->eventStore->storeEvent(
                Event::EVENT_TYPE_RESERVATION_CANCELLED,
                Event::AGGREGATE_TYPE_RESERVATION,
                $reservation->id,
                ['cancelled_at' => now()->toDateTimeString()]
            );

            // Invalidate cache
            Cache::tags(['reservations', "user_{$reservation->user_id}", "copy_{$reservation->book_copy_id}"])->flush();

            return $reservation;
        });
    }

    public function getReservationQueue(BookCopy $copy)
    {
        return Cache::tags(["copy_{$copy->id}", 'reservations'])->remember(
            "reservation_queue_{$copy->id}",
            now()->addMinutes(10),
            function () use ($copy) {
                return Reservation::where('book_copy_id', $copy->id)
                    ->where('status', 'pending')
                    ->orderBy('queue_position')
                    ->with('user')
                    ->get();
            }
        );
    }

    public function notifyNextInQueue(BookCopy $copy)
    {
        $nextReservation = Reservation::where('book_copy_id', $copy->id)
            ->where('status', 'pending')
            ->orderBy('queue_position')
            ->first();

        if ($nextReservation) {
            // Add to queue for async processing
            $this->queueService->addToQueue('notify_user', [
                'user_id' => $nextReservation->user_id,
                'notification_type' => 'book_available',
                'message' => "The book you reserved is now available: {$copy->book->title}"
            ]);

            return true;
        }

        return false;
    }

    public function isUserEligibleForReservation(User $user, BookCopy $copy)
    {
        // Special copy for VIP users only
        if ($copy->is_special && !$user->is_vip) {
            return false;
        }

        // Check user score (eligibility)
        $eligibility = $this->scoreService->getUserEligibility($user);
        if ($eligibility < User::MINIMUM_SCORE_REQUIRED) { // Minimum score required
            return false;
        }

        // Check maximum allowed reservations
        $activeReservations = $user->reservations()
            ->whereIn('status', [Reservation::STATUS_ACTIVE, Reservation::STATUS_PENDING])
            ->count();

        $maxReservations = $user->is_vip ? User::MAXIMUM_RESERVATION_VIP_USER : User::MAXIMUM_RESERVATION_USER; // VIP users get more reservations
        if ($activeReservations >= $maxReservations) {
            return false;
        }

        return true;
    }

    public function handleChainedReservation(BookCopy $copy)
    {
        $nextInQueue = Reservation::where('book_copy_id', $copy->id)
            ->where('status', Reservation::STATUS_PENDING)
            ->orderBy('queue_position')
            ->first();

        if ($nextInQueue) {
            DB::transaction(function () use ($nextInQueue, $copy) {
                // Optimistic locking
                $currentVersion = $nextInQueue->version;
                $updated = Reservation::where('id', $nextInQueue->id)
                    ->where('version', $currentVersion)
                    ->update([
                        'status' => Reservation::STATUS_ACTIVE,
                        'version' => $currentVersion + 1
                    ]);

                if (!$updated) {
                    throw new \Exception('کتاب مورد نظر توسط فرد دیگری در حالا رزرو است.');
                }

                $copy->status = BookCopy::STATUS_RESERVED;
                $copy->save();

                // Notify user
                $this->notificationService->sendNotification(
                    $nextInQueue->user,
                    Notification::BOOK_AVAILABLE,
                    "The book you reserved is now available: {$copy->book->title}"
                );

                // Store event
                $this->eventStore->storeEvent(
                    Event::EVENT_TYPE_RESERVATION_ACTIVATED,
                    Event::AGGREGATE_TYPE_RESERVATION,
                    $nextInQueue->id,
                    ['activated_at' => now()->toDateTimeString()]
                );
            });

            return true;
        }

        return false;
    }
}
