<?php

namespace Database\Factories;

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
    public function definition()
    {
        $price =  rand(500,2000);
        $title = $this->faker->unique()->realTextBetween(20,30);
        return [
            'title' => $title,
            'slug'=> Str::slug($title),
            'picture'=> $this->faker->imageUrl(),
            'price' => $price,
            'discount_price' => $this->faker->boolean(10) ? $price - rand(100,300) : 0,
            'currency' => $this->faker->randomElement(['USD','EUR']),
            'qty' => rand(0,10),
        ];
    }
}
