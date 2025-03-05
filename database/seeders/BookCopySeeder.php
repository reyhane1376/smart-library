<?php

namespace Database\Seeders;

use App\Models\BookCopy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookCopySeeder extends Seeder
{
    public function run(): void
    {
        $bookCopies = [];
        for ($bookId = 1; $bookId <= 5; $bookId++) {
            for ($i = 1; $i <= 3; $i++) {
                $statuses = ['available', 'reserved', 'borrowed', 'under_repair'];
                $conditions = array_keys(BookCopy::CONDITION_TITLE);
                
                $bookCopies[] = [
                    'book_id'            => $bookId,
                    'physical_condition' => $conditions[array_rand($conditions)],
                    'status'             => $statuses[array_rand($statuses)],
                    'location'           => rand(1, 3),                             // شعبه‌های ۱ تا ۳
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
        }

        DB::table('book_copies')->insert($bookCopies);
    }
}
