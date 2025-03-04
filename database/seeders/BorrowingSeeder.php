<?php

namespace Database\Seeders;

use App\Models\BookCopy;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BorrowingSeeder extends Seeder
{
    public function run(): void
    {
        $borrowings = [];
        
        $borrowedCopies = DB::table('book_copies')
            ->where('status', BookCopy::STATUS_BORROWED)
            ->pluck('id')
            ->toArray();
            
        foreach ($borrowedCopies as $copyId) {
            $borrowedAt = Carbon::now()->subDays(rand(5, 30));
            $dueDate = (clone $borrowedAt)->addDays(14); // دو هفته مهلت
            $isReturned = rand(0, 1);
            
            $returnedAt = null;
            $returnCondition = null;
            $delayDays = 0;
            $fineAmount = 0;
            
            if ($isReturned) {
                $returnedAt = (clone $dueDate)->addDays(rand(-7, 10)); // بین ۷ روز زودتر تا ۱۰ روز دیرتر
                $returnCondition = ['Excellent', 'Good', 'Average', 'Damaged'][rand(0, 3)];
                
                if ($returnedAt > $dueDate) {
                    $delayDays = $returnedAt->diffInDays($dueDate);
                    $fineAmount = $delayDays * 5000; // هر روز تاخیر ۵۰۰۰ تومان جریمه
                }
            }
            
            $borrowings[] = [
                'user_id'          => rand(1, 4),
                'book_copy_id'     => $copyId,
                'borrowed_at'      => $borrowedAt,
                'due_date'         => $dueDate,
                'returned_at'      => $returnedAt,
                'return_condition' => $returnCondition,
                'delay_days'       => $delayDays,
                'fine_amount'      => $fineAmount,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        DB::table('borrowings')->insert($borrowings);
    }
}
