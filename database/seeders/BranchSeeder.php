<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'شعبه مرکزی',
                'address' => 'تهران، خیابان انقلاب، شماره ۱۲۳',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'شعبه شمال',
                'address' => 'تهران، نیاوران، خیابان باهنر، شماره ۴۵',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'شعبه غرب',
                'address' => 'تهران، بلوار فردوس، خیابان سازمان آب، شماره ۷۸',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('branches')->insert($branches);
    }
}
