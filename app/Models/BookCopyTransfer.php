<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookCopyTransfer extends Model
{
    protected $fillable = [
        'book_copy_id',
        'from_branch_id',
        'to_branch_id',
        'status'
    ];

    const STATUS_REQUESTED  = 'requested';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_COMPLETED  = 'completed'; 

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }
}
