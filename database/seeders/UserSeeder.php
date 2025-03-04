<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'              => 'ریحانه ابراهیمی',
                'email'             => 'reyhaneebrahimi27@yahoo.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('123456789'),
                'is_vip'            => true,
                'score'             => 120,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'مریم حسینی',
                'email'             => 'maryam@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password123'),
                'is_vip'            => false,
                'score'             => 100,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'رضا کریمی',
                'email'             => 'reza@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password123'),
                'is_vip'            => false,
                'score'             => 95,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'سارا احمدی',
                'email'             => 'sara@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password123'),
                'is_vip'            => true,
                'score'             => 150,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
