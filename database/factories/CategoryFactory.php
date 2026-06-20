<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Makanan', 'Transportasi', 'Belanja', 'Hiburan', 'Kesehatan', 'Pendidikan', 'Utilitas', 'Gaji', 'Freelance', 'Investasi']),
            'type' => fake()->randomElement(['income', 'expense', 'both']),
            'icon' => fake()->randomElement(['🍔', '🚗', '🛍️', '🎬', '⚕️', '📚', '⚡', '💰', '💵', '📈']),
            'color' => fake()->randomElement(['orange', 'blue', 'pink', 'purple', 'red', 'green', 'yellow']),
        ];
    }
}