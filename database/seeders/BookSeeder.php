<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title' => 'بوف کور',
                'author' => 'صادق هدایت',
                'description' => 'رمانی از صادق هدایت که یکی از مهم‌ترین آثار ادبیات داستانی ایران است.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'سووشون',
                'author' => 'سیمین دانشور',
                'description' => 'رمانی به قلم سیمین دانشور که به عنوان اولین رمان نوشته شده توسط یک زن ایرانی شناخته می‌شود.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'شازده احتجاب',
                'author' => 'هوشنگ گلشیری',
                'description' => 'رمانی از هوشنگ گلشیری که در سال ۱۳۴۷ منتشر شد و یکی از آثار برجسته ادبیات معاصر ایران است.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'چشم‌هایش',
                'author' => 'بزرگ علوی',
                'description' => 'رمانی از بزرگ علوی که در سال ۱۳۳۱ منتشر شد و به عنوان یکی از آثار کلاسیک ادبیات معاصر ایران شناخته می‌شود.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'کلیدر',
                'author' => 'محمود دولت‌آبادی',
                'description' => 'رمان حماسی اثر محمود دولت‌آبادی که در دهه ۱۳۵۰ در ده جلد منتشر شد.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('books')->insert($books);
    }
}
