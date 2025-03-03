<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopyRepairHistory extends Model
{
    protected $fillable = [
        'book_copy_id', 
        'repair_details', 
        'repair_date'
    ];

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }
}
