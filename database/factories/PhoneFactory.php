<?php

namespace Database\Factories;

use App\Models\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Phone>
 */
class PhoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand' => fake()->randomElement(['Iphone', 'Samsung', 'Xiaomi', 'Oppo']),
            'model' => fake()->word(),
            'price' => fake()->randomFloat(2, 100, 1000),
            'image' => 'https://picsum.photos/400/300?random='.rand(1, 1000),
            'store_url' => fake()->url(),
            'ram' => fake()->randomElement(['4GB', '8GB', '12GB', '16GB']),
            'storage' => fake()->randomElement(['64GB', '128GB', '256GB', '512GB']),
            'camera' => fake()->randomElement(['12MP', '24MP', '50MP', '64MP']),
            'battery' => fake()->randomElement(['5000mAh', '6000mAh', '7000mAh', '8000mAh']),
            'screen_size' => fake()->randomElement(['6.1 inch', '6.5 inch', '6.7 inch']),
            'os' => fake()->randomElement(['Android 13', 'Android 14', 'iOS 16', 'iOS 17']),
            'release_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'rating' => fake()->randomFloat(1, 3, 5),
        ];
    }
}
