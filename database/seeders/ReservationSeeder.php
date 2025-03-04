<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = [];
        
        // گرفتن لیست کتاب‌هایی که وضعیت آنها "reserved" است
        $reservedCopies = DB::table('book_copies')
            ->where('status', 'reserved')
            ->pluck('id')
            ->toArray();
            
        $statuses = ['pending', 'active', 'cancelled', 'completed'];
        
        foreach ($reservedCopies as $copyId) {
            $reservedAt = Carbon::now()->subDays(rand(1, 10));
            $expiresAt = (clone $reservedAt)->addDays(3); // ۳ روز مهلت رزرو
            
            $reservations[] = [
                'user_id'        => rand(1, 4),
                'book_copy_id'   => $copyId,
                'reserved_at'    => $reservedAt,
                'expires_at'     => $expiresAt,
                'status'         => $statuses[array_rand($statuses)],
                'queue_position' => rand(0, 3),
                'version'        => 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        DB::table('reservations')->insert($reservations);
    }
}
