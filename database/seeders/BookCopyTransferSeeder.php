<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookCopyTransferSeeder extends Seeder
{
    public function run(): void
    {
        $transfers = [];
        $statuses = ['requested', 'in_transit', 'completed'];
        
        for ($i = 1; $i <= 5; $i++) {
            $fromBranch = rand(1, 3);
            $toBranch = rand(1, 3);
            
            // مطمئن شویم شعبه مبدا و مقصد یکسان نباشند
            while ($toBranch == $fromBranch) {
                $toBranch = rand(1, 3);
            }
            
            $transfers[] = [
                'book_copy_id'   => rand(1, 15),                        // با فرض اینکه ۱۵ نسخه کتاب داریم
                'from_branch_id' => $fromBranch,
                'to_branch_id'   => $toBranch,
                'status'         => $statuses[array_rand($statuses)],
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        DB::table('book_copy_transfers')->insert($transfers);
    }
}
