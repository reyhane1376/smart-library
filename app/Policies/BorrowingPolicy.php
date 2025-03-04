<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    public function __construct()
    {
        //
    }

    public function return(User $user, Borrowing $borrowing)
    {
        return $user->id === $borrowing->user_id;
    }
}
