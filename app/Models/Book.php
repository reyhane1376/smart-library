<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'description'];

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }
}
