<?php

namespace App\Interfaces;

use App\Models\User;

interface ScoreServiceInterface
{
    public function calculateUserScore(User $user);
    public function updateScoreForDelay(User $user, $delayDays);
    public function updateScoreForDamage(User $user, $damageLevel);
    public function updateScoreForCancelledReservations(User $user);
    public function getUserEligibility(User $user);
}