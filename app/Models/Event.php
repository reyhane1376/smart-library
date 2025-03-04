<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_type',
        'aggregate_type',
        'aggregate_id',
        'event_data',
        'occurred_at'
    ];

    protected $casts = [
        'event_data' => 'array',
        'occurred_at' => 'datetime',
    ];


    const EVENT_TYPE_BOOK_CREATED          = 'book_created';
    const EVENT_TYPE_BOOK_BORROWED         = 'book_borrowed';
    const EVENT_TYPE_BOOK_RETURNED         = 'book_returned';
    const EVENT_TYPE_RESERVATION_CREATED   = 'reservation_created';
    const EVENT_TYPE_RESERVATION_CANCELLED = 'reservation_cancelled';
    const EVENT_TYPE_RESERVATION_ACTIVATED = 'reservation_activated';
    const EVENT_TYPE_BORROWING_EXTENDED    = 'borrowing_extended';


    const AGGREGATE_TYPE_BOOK        = 'book';
    const AGGREGATE_TYPE_BOOK_COPY   = 'book_copy';
    const AGGREGATE_TYPE_BORROWING   = 'borrowing';
    const AGGREGATE_TYPE_RESERVATION = 'reservation';



}
