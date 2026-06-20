<?php

namespace Database\Factories;

use App\Models\SavingGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavingGoalFactory extends Factory
{
    protected $model = SavingGoal::class;

    public function definition(): array
    {
        $target = fake()->randomFloat(2, 5000000, 50000000);

        return [
            'name' => fake()->randomElement(['Liburan ke Bali', 'Beli Laptop Baru', 'Dana Darurat', 'Beli Motor', 'Nikahan', 'DP Rumah']),
            'target_amount' => $target,
            'current_amount' => fake()->randomFloat(2, 0, $target * 0.8),
            'deadline' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'icon' => fake()->randomElement(['✈️', '💻', '🛡️', '🏍️', '💍', '🏠']),
        ];
    }
}