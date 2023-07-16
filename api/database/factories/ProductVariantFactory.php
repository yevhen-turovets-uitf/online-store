<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\VariantSize;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $price = rand(600,1700);
        $title = $this->faker->unique()->realTextBetween(20,50);
        return [
            'title' => $title,
            'slug'=> Str::slug($title),
            'price' => $price,
            'discount_price' => $this->faker->boolean(20) ? $price - rand(200,400) : 0,
            'currency' => $this->faker->randomElement(['USD','EUR']),
            'qty' => rand(0, 25),

            'product_id' => Product::factory(),
            'size_id' => VariantSize::all()->unique()->random(),
        ];
    }
}
