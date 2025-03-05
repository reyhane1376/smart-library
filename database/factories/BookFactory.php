<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'author' => $this->faker->name(),
            'description' => $this->faker->paragraph(2)
        ];
    }

    public function withCopies($count = 3)
    {
        return $this->afterCreating(function (Book $book) use($count) {
            \App\Models\BookCopy::factory()->count($count)->create([
                'book_id' => $book->id
            ]);
        });
    }
}
