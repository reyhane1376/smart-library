<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address'
    ];

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(BookCopyTransfer::class, 'to_branch_id');
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(BookCopyTransfer::class, 'from_branch_id');
    }
}
