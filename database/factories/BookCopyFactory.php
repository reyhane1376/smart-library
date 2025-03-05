<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookCopyFactory extends Factory
{
    protected $model = BookCopy::class;

    public function definition(): array
    {
        $book = Book::first() ?? Book::factory()->create();
        $branch = Branch::first() ?? Branch::factory()->create();

        return [
            'book_id' => $book->id,
            'physical_condition' => $this->faker->randomElement([
                BookCopy::CONDITION_EXCELLENT,
                BookCopy::CONDITION_GOOD,
                BookCopy::CONDITION_AVERAGE,
                BookCopy::CONDITION_NEEDS_REPAIR
            ]),
            'status' => $this->faker->randomElement([
                BookCopy::STATUS_AVAILABLE,
                BookCopy::STATUS_RESERVED,
                BookCopy::STATUS_BORROWED,
                BookCopy::STATUS_UNDER_REPAIR,
                BookCopy::STATUS_DAMAGE,
                BookCopy::STATUS_IN_TRANSFER
            ]),
            'is_special' => $this->faker->boolean(20),
            'branch_id' => $branch->id,
            'version' => $this->faker->numberBetween(1, 5),
            'location' => $this->faker->word
        ];
    }
}
