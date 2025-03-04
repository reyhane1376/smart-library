<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            UserSeeder::class,
            BookSeeder::class,
            BookCopySeeder::class,
            BorrowingSeeder::class,
            ReservationSeeder::class,
            NotificationSeeder::class,
            BookCopyTransferSeeder::class,
            CopyRepairHistorySeeder::class,
            EventSeeder::class,
        ]);
    }
}
