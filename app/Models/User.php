<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_vip',
        'score'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_vip' => 'boolean',
    ];


    CONST MINIMUM_SCORE_REQUIRED = 50;
    CONST MAXIMUM_RESERVATION_VIP_USER = 10;
    CONST MAXIMUM_RESERVATION_USER = 5;
    CONST BORROW_PERIOD_VIP_USER = 30;
    CONST BORROW_PERIOD_USER = 14;
    CONST FINE_FOR_ONE_DAY = 1000;

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
