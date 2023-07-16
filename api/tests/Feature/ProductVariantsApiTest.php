<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VariantSize;
use Database\Seeders\VariantSizeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductVariantsApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private mixed $user;
    private mixed $existing_product;
    private mixed $existing_variant;
    private mixed $product;
    private mixed $variant;
    private mixed $size;
    private mixed $size2;
    private mixed $size3;

    //    protected $seeder = VariantSizeSeeder::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(
            [
                'password' => Hash::make('TestPass1'),
            ]
        );
        $this->size = VariantSize::factory()->create(['name' => "LFD"]);
        $this->size2 = VariantSize::factory()->create(['name' => "MSJ"]);
        $this->size3 = VariantSize::factory()->create(['name' => "DNJ"]);
        $this->product = Product::factory()->create();
        $this->existing_product = Product::factory()->create();
        $this->variant = ProductVariant::factory()->create(
            ['product_id' => $this->product->id, 'size_id' => $this->size3->id]
        );
        $this->existing_variant = ProductVariant::factory()->create(
            ['product_id' => $this->existing_product->id, 'size_id' => $this->size3->id]
        );
    }

    public function test_get_all_variants_for_product_successful()
    {
        $this->actingAs($this->user);
        $product = $this->product->id;
        $response = $this->getJson(route('products.variants.index', ['product' => $product]));

        $response->assertOk()->assertJsonStructure(
            [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'price',
                    'discountPrice',
                    'currency',
                    'qty',
                    'size',
                    'productId',
                    'sizeId',
                ],
            ]
        )->assertJsonCount(ProductVariant::query()->where('product_id', $product)->count());
    }

    public function test_get_variant_successful()
    {
        $this->actingAs($this->user);
        $variant = $this->variant->id;
        $response = $this->getJson(
            route('variants.show', ['variant' => $variant])
        );

        $response->assertOk()->assertJsonFragment(
            [
                'id' => $variant,
                'title' => $this->variant->title,
                'price' => $this->variant->price,
                'discountPrice' => $this->variant->discount_price,
                'currency' => $this->variant->currency,
                'qty' => $this->variant->qty,
            ],
        );
    }

    public function test_variant_not_found()
    {
        $this->actingAs($this->user);
        $variant = 9999;
        $response = $this->getJson(
            route('variants.show', ['variant' => $variant])
        );

        $response->assertNotFound();
    }

    public function test_create_variant_successful()
    {
        $this->actingAs($this->user);
        $product = $this->product->id;
        $title = Str::random(31);
        $price = 777;
        $response = $this->postJson(
            route('products.variants.store', ['product' => $product]),
            [
                'title' => $title,
                'price' => $price,
                'size' => $this->size->id,
            ]
        );

        $response->assertOk()->assertJsonStructure(
            [
                'id',
                'title',
                'slug',
                'price',
                'discountPrice',
                'currency',
                'qty',
                'size',
                'productId',
                'sizeId',
            ]
        )->assertJsonFragment(
            [
                'title' => $title,
                'price' => $price,
                'productId' => $product,
                'sizeId' => $this->size->id,
            ]
        );
        $this->assertDatabaseHas(
            'product_variants',
            [
                'title' => $title,
                'price' => $price,
                'product_id' => $product,
            ]
        );
    }

    public function test_create_variant_title_already_exist()
    {
        $this->actingAs($this->user);
        $product = $this->product->id;
        $response = $this->postJson(
            route('products.variants.store', ['product' => $product]),
            [
                'title' => $this->existing_variant->title,
                'price' => 888,
                'size' => $this->size->id,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'title' => __('validation.unique', ['attribute' => 'title']),
            ]
        );
    }

    public function test_create_product_empty_request()
    {
        $this->actingAs($this->user);
        $product = $this->product->id;
        $response = $this->postJson(
            route('products.variants.store', ['product' => $product]),
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'title' => __('validation.required', ['attribute' => 'title']),
                'price' => __('validation.required', ['attribute' => 'price']),
            ]
        );
    }

    public function test_create_product_incorrect_currency()
    {
        $this->actingAs($this->user);
        $product = $this->product->id;
        $response = $this->postJson(
            route('products.variants.store', ['product' => $product]),
            [
                'title' => Str::random(21),
                'price' => 123,
                'currency' => 'wrong',
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'currency' => __('validation.in', ['attribute' => 'currency']),
            ]
        );
    }

    public function test_create_product_discount_price_and_qty_must_be_greater_than_or_equal_0()
    {
        $this->actingAs($this->user);
        $product = $this->product->id;
        $response = $this->postJson(
            route('products.variants.store', ['product' => $product]),
            [
                'title' => Str::random(21),
                'discountPrice' => -1,
                'qty' => -1,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'discountPrice' => __('validation.gte.numeric', ['attribute' => 'discount price', 'value' => 0]),
                'qty' => __('validation.gte.numeric', ['attribute' => 'qty', 'value' => 0]),
            ]
        );
    }

    public function test_create_product_price_must_be_greater_than_0()
    {
        $this->actingAs($this->user);
        $product = $this->product->id;
        $response = $this->postJson(
            route('products.variants.store', ['product' => $product]),
            [
                'title' => Str::random(21),
                'price' => 0,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'price' => __('validation.gt.numeric', ['attribute' => 'price', 'value' => 0]),
            ]
        );
    }

    public function test_edit_product_successful()
    {
        $this->actingAs($this->user);
        $variant = $this->variant->id;
        $title = Str::random(26);
        $response = $this->putJson(
            route('variants.update', ['variant' => $variant]),
            [
                'title' => $title,
                'price' => 999,
            ]
        );

        $response->assertOk()->assertJsonFragment(
            [
                'id' => $variant,
                'title' => $title,
                'price' => 999,
            ]
        );
    }

    public function test_edit_product_title_already_exist()
    {
        $this->actingAs($this->user);
        $variant = $this->variant->id;
        $response = $this->putJson(
            route('variants.update', ['variant' => $variant]),
            [
                'title' => $this->existing_variant->title,
                'price' => 888,
                'size' => $this->size2->id,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'title' => __('validation.unique', ['attribute' => 'title']),
            ]
        );
    }

    public function test_delete_variant_successful()
    {
        $this->actingAs($this->user);

        $variant = $this->variant->id;
        $this->assertDatabaseHas('product_variants', ['id' => $variant]);
        $response = $this->deleteJson(route('variants.destroy', ['variant' => $variant]));

        $response->assertNoContent();
        $this->assertDatabaseMissing('product_variants', ['id' => $variant]);
    }

    public function test_delete_variant_not_found()
    {
        $this->actingAs($this->user);
        $variant = $this->existing_variant->id;
        $this->existing_variant->delete();
        $response = $this->deleteJson(
            route('variants.destroy', ['variant' => $variant])
        );

        $response->assertNotFound();
    }

    public function test_unauthorized()
    {
        $response = $this->getJson(route('products.variants.index', ['product' => $this->product->id]));
        $response->assertUnauthorized();
        $response = $this->getJson(route('variants.show', ['variant' => $this->variant->id]));
        $response->assertUnauthorized();
        $response = $this->postJson(
            route('products.variants.store', ['product' => $this->product->id]),
            [
                'title' => Str::random(17),
                'price' => 789,
                'size' => $this->size->id,
            ]
        );
        $response->assertUnauthorized();
        $response = $this->putJson(
            route('variants.update', ['variant' => $this->variant->id]),
            [
                'title' => Str::random(19),
                'price' => 789,
                'size' => $this->size2->id,
            ]
        );
        $response->assertUnauthorized();
        $response = $this->deleteJson(route('variants.destroy', ['variant' => $this->variant->id]));
        $response->assertUnauthorized();
    }
}
