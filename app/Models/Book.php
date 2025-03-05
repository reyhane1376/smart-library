<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'author', 'description'];

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    protected static function newFactory()
    {
        return BookFactory::new();
    }
}
