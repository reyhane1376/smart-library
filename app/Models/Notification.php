<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read',
        'retry_count',
        'sent_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];


    public const DUE_DATE_REMINDER  = 'due_date_reminder';
    public const RESERVATION_EXPIRY = 'reservation_expiry';
    public const NEW_BOOK_AVAILABLE = 'new_book_available';
    public const BOOK_AVAILABLE     = 'book_available';
    public const FINE_NOTIFICATION  = 'fine_notification';


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
