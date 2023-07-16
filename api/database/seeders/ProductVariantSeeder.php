<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        foreach ($products as $product)
        {
            if (fake()->boolean(30))
            {
                $count = rand(1,5);
                $sizes = VariantSize::all()->random($count);
                ProductVariant::factory($count)
                    ->sequence(fn ($sequence) => ['size_id' => $sizes->pull($sequence->index)])
                    ->create([
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}
