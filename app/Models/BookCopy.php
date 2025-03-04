<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCopy extends Model
{
    protected $fillable = [
        'book_id', 
        'physical_condition', 
        'status', 
        'is_special',
        'version',
        'location'
    ];


    /* --------------------- CONDITION CONST ----------------------------- */

    const CONDITION_EXCELLENT    = 'عالی';
    const CONDITION_GOOD         = 'خوب';
    const CONDITION_AVERAGE      = 'متوسط';
    const CONDITION_NEEDS_REPAIR = 'نیاز به تعمیر';


    /* --------------------- STATUS CONST ----------------------------- */

    const STATUS_AVAILABLE    = 'available';
    const STATUS_RESERVED     = 'reserved';
    const STATUS_BORROWED     = 'borrowed';
    const STATUS_UNDER_REPAIR = 'under_repair';


    /* --------------------- Relation ----------------------------- */

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function repairHistories(): HasMany
    {
        return $this->hasMany(CopyRepairHistory::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(BookCopyTransfer::class);
    }
}
