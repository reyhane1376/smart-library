<?php

namespace App\Interfaces;

use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\User;

interface BorrowingServiceInterface
{
    public function borrowBook(User $user, BookCopy $copy);
    public function returnBook(Borrowing $borrowing, $condition);
    public function calculateFine(Borrowing $borrowing);
    public function extendBorrowingPeriod(Borrowing $borrowing, $days);
}