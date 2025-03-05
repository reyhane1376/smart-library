<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password123'),             // Default test password
            'remember_token'    => Str::random(10),
            'is_vip'            => $this->faker->boolean(20),             // 20% chance of being VIP
            'score'             => $this->faker->numberBetween(0, 100)
        ];
    }

    public function vip()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_vip' => true,
                'score' => $this->faker->numberBetween(50, 100)
            ];
        });
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
