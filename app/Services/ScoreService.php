<?php

namespace App\Services;

use App\Interfaces\ScoreServiceInterface;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\User;

class ScoreService implements ScoreServiceInterface
{
    public function calculateUserScore(User $user)
    {
        // Base score
        $score = 100;
        
        // Deduct for late returns
        $lateBorrowings = Borrowing::where('user_id', $user->id)
            ->where('delay_days', '>', 0)
            ->get();
            
        foreach ($lateBorrowings as $borrowing) {
            $score -= min($borrowing->delay_days * 2, 20); // Max 20 points per late return
        }
        
        // Deduct for damages
        $damagedReturns = Borrowing::where('user_id', $user->id)
            ->where('return_condition', Borrowing::DAMAGED)
            ->count();
            
        $score -= $damagedReturns * 10; // 10 points per damaged book
        
        // Deduct for cancelled reservations
        $cancelledReservations = Reservation::where('user_id', $user->id)
            ->where('status', Reservation::STATUS_CANCELLED)
            ->count();
            
        $score -= $cancelledReservations * 5; // 5 points per cancellation
        
        // Bonus for on-time returns
        $onTimeReturns = Borrowing::where('user_id', $user->id)
            ->whereNotNull('returned_at')
            ->where('delay_days', 0)
            ->count();
            
        $score += min($onTimeReturns * 2, 20); // Max 20 bonus points for on-time returns
        
        // Ensure score is within bounds
        return max(min($score, 100), 0);
    }
    
    public function updateScoreForDelay(User $user, $delayDays)
    {
        $penaltyPoints = min($delayDays * 2, 20); // Max 20 points penalty
        $newScore = max($user->score - $penaltyPoints, 0);
        
        $user->score = $newScore;
        $user->save();
        
        return $user;
    }
    
    public function updateScoreForDamage(User $user, $damageLevel)
    {
        $penaltyPoints = 10; // Default for 'damaged'
        
        if ($damageLevel === 'severely_damaged') {
            $penaltyPoints = 20;
        } elseif ($damageLevel === 'slightly_damaged') {
            $penaltyPoints = 5;
        }
        
        $newScore = max($user->score - $penaltyPoints, 0);
        
        $user->score = $newScore;
        $user->save();
        
        return $user;
    }
    
    public function updateScoreForCancelledReservations(User $user)
    {
        $penaltyPoints = 5; // 5 points penalty per cancellation
        $newScore = max($user->score - $penaltyPoints, 0);
        
        $user->score = $newScore;
        $user->save();
        
        return $user;
    }
    
    public function getUserEligibility(User $user)
    {
        // Recalculate the full score
        $calculatedScore = $this->calculateUserScore($user);
        
        // Update user's stored score
        $user->score = $calculatedScore;
        $user->save();
        
        return $calculatedScore;
    }
}