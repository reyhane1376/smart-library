<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events         = [];
        $eventTypes     = ['book_created', 'book_borrowed', 'book_returned', 'reservation_created', 'reservation_cancelled'];
        $aggregateTypes = ['book', 'book_copy', 'borrowing', 'reservation'];
        
        for ($i = 1; $i <= 30; $i++) {
            $eventType = $eventTypes[array_rand($eventTypes)];
            $aggregateType = $aggregateTypes[array_rand($aggregateTypes)];
            
            // تولید داده‌های رویداد مناسب با نوع رویداد
            $eventData = match($eventType) {
                'book_created' => json_encode([
                    'title'      => 'عنوان کتاب جدید',
                    'author'     => 'نام نویسنده',
                    'created_by' => 'مدیر سیستم'
                ]),
                'book_borrowed' => json_encode([
                    'book_copy_id' => rand(1, 15),
                    'user_id'      => rand(1, 4),
                    'due_date'     => Carbon::now()->addDays(14)->format('Y-m-d')
                ]),
                'book_returned' => json_encode([
                    'book_copy_id' => rand(1, 15),
                    'user_id'      => rand(1, 4),
                    'condition'    => ['عالی', 'خوب', 'متوسط', 'آسیب دیده'][rand(0, 3)]
                ]),
                'reservation_created' => json_encode([
                    'book_copy_id' => rand(1, 15),
                    'user_id'      => rand(1, 4),
                    'expires_at'   => Carbon::now()->addDays(3)->format('Y-m-d H:i:s')
                ]),
                'reservation_cancelled' => json_encode([
                    'reservation_id' => rand(1, 10),
                    'cancelled_by'   => rand(1, 4),
                    'reason'         => 'انصراف کاربر'
                ]),
            };
            
            $events[] = [
                'event_type'     => $eventType,
                'aggregate_type' => $aggregateType,
                'aggregate_id'   => rand(1, 15),
                'event_data'     => $eventData,
                'occurred_at'    => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        DB::table('events')->insert($events);
    }
}
