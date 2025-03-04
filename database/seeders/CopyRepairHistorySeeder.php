<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CopyRepairHistorySeeder extends Seeder
{
    public function run(): void
    {
        $repairs = [];
        
        // گرفتن لیست کتاب‌هایی که وضعیت آنها "under_repair" است
        $repairCopies = DB::table('book_copies')
            ->where('status', 'under_repair')
            ->orWhere('physical_condition', 'نیاز به تعمیر')
            ->pluck('id')
            ->toArray();
            
        foreach ($repairCopies as $copyId) {
            $repairDetails = [
                'تعویض جلد کتاب که آسیب دیده بود.',
                'صحافی مجدد به دلیل جدا شدن صفحات.',
                'ترمیم پارگی صفحات ۳۰ تا ۴۵.',
                'تعویض برچسب شناسایی کتاب.',
            ];
            
            $repairs[] = [
                'book_copy_id'   => $copyId,
                'repair_details' => $repairDetails[array_rand($repairDetails)],
                'repair_date'    => Carbon::now()->subDays(rand(1, 30)),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        DB::table('copy_repair_histories')->insert($repairs);
    }
}
