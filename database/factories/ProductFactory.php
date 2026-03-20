<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'      => (string) Str::uuid(),
            'user_id' => User::factory(),
            'name'    => $this->faker->words(3, true),
            'barcode' => $this->faker->unique()->ean13(),
            'price'   => $this->faker->randomFloat(2, 1, 1000),
            'stock'   => $this->faker->numberBetween(0, 100),
        ];
    }
}
