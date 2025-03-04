<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function cancel(User $user, Reservation $reservation)
    {
        return $user->id === $reservation->user_id;
    }
}
