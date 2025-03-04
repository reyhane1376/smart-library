<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [];
        $types = ['due_date_reminder', 'reservation_expiry', 'new_book_available', 'fine_notification'];
        
        for ($i = 1; $i <= 20; $i++) {
            $type = $types[array_rand($types)];
            $message = match($type) {
                'due_date_reminder'  => 'یادآوری: مهلت بازگرداندن کتاب شما تا ۲ روز دیگر به پایان می‌رسد.',
                'reservation_expiry' => 'رزرو کتاب شما تا فردا معتبر است. لطفاً برای تحویل گرفتن کتاب مراجعه کنید.',
                'new_book_available' => 'کتاب جدید مورد علاقه شما در کتابخانه موجود شده است.',
                'fine_notification'  => 'شما بابت تاخیر در بازگرداندن کتاب مشمول جریمه شده‌اید.',
            };
            
            $notifications[] = [
                'user_id'     => rand(1, 4),
                'type'        => $type,
                'message'     => $message,
                'is_read'     => rand(0, 1),
                'retry_count' => rand(0, 3),
                'sent_at'     => rand(0, 1) ? Carbon::now()->subDays(rand(1, 10)) : null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        DB::table('notifications')->insert($notifications);
    }
}
