<?php

namespace Database\Factories;

use App\Models\Phone;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone_id'=> fn() => Phone::inRandomOrder()->first()->id,
            'user_id' => fn() => User::inRandomOrder()->first()->id,
            'comment' => fake()->sentence(5),
            'rating' => fake()->randomFloat(1, 1, 5),
        ];
    }
}
