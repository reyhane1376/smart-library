<?php

namespace App\Interfaces;

use App\Models\BookCopy;
use App\Models\Reservation;
use App\Models\User;

interface ReservationServiceInterface
{
    public function reserveBook(User $user, BookCopy $copy);
    public function cancelReservation(Reservation $reservation);
    public function getReservationQueue(BookCopy $copy);
    public function notifyNextInQueue(BookCopy $copy);
    public function isUserEligibleForReservation(User $user, BookCopy $copy);
    public function handleChainedReservation(BookCopy $copy);
}