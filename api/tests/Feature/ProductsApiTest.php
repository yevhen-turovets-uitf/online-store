<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductsApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $product_api_url;
    private mixed $user;
    private mixed $existing_product;
    private mixed $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product_api_url = route('products.index');

        $this->user = User::factory()->create(
            [
                'password' => Hash::make('Testpass1'),
            ]
        );
        $this->product = Product::factory()->create();
        $this->existing_product = Product::factory()->create();
    }

    public function test_get_all_products_successful()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(
            $this->product_api_url
        );

        $response->assertOk()->assertJsonStructure(
            [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'picture',
                    'price',
                    'discountPrice',
                    'currency',
                    'qty',
                    'variants',
                ],
            ]
        )->assertJsonCount(Product::all()->count());
    }

    public function test_get_product_successful()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(
            $this->product_api_url.'/'.$this->product->id,
        );

        $response->assertOk()->assertJsonFragment(
            [
                'id' => $this->product->id,
                'title' => $this->product->title,
                'price' => $this->product->price,
                'discountPrice' => $this->product->discount_price,
                'currency' => $this->product->currency,
                'qty' => $this->product->qty,
            ],
        );
    }

    public function test_product_not_found()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(
            $this->product_api_url.'/999',
        );

        $response->assertNotFound();
    }

    public function test_create_product_successful()
    {
        $this->actingAs($this->user);
        $title = Str::random(33);
        $price = 777;
        $response = $this->postJson(
            $this->product_api_url,
            [
                'title' => $title,
                'price' => $price,
            ]
        );

        $response->assertOk()->assertJsonFragment(
            [
                'title' => $title,
                'price' => $price,
            ]
        );
        $this->assertDatabaseHas(
            'products',
            [
                'title' => $title,
                'price' => $price,
            ]
        );
    }

    public function test_create_product_title_already_exist()
    {
        $this->actingAs($this->user);
        $response = $this->postJson(
            $this->product_api_url,
            [
                'title' => $this->existing_product->title,
                'price' => 888,
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
        $response = $this->postJson(
            $this->product_api_url
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
        $response = $this->postJson(
            $this->product_api_url,
            [
                'title' => Str::random(21),
                'price' => 123,
                'currency' => 'wrong'
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
        $response = $this->postJson(
            $this->product_api_url,
            [
                'title' => Str::random(21),
                'discountPrice' => -1,
                'qty' => -1
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'discountPrice' => __('validation.gte.numeric', ['attribute' => 'discount price','value' => 0]),
                'qty' => __('validation.gte.numeric', ['attribute' => 'qty','value' => 0]),
            ]
        );
    }

    public function test_create_product_price_must_be_greater_than_0()
    {
        $this->actingAs($this->user);
        $response = $this->postJson(
            $this->product_api_url,
            [
                'title' => Str::random(21),
                'price' => 0,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'price' => __('validation.gt.numeric', ['attribute' => 'price','value' => 0]),
            ]
        );
    }

    public function test_edit_product_successful()
    {
        $this->actingAs($this->user);
        $title = Str::random(26);
        $response = $this->putJson(
            $this->product_api_url.'/'.$this->product->id,
            [
                'title' => $title,
                'price' => 999,
            ]
        );

        $response->assertOk()->assertJsonFragment(
            [
                'id' => $this->product->id,
                'title' => $title,
                'price' => 999,
            ]
        );
    }

    public function test_edit_product_title_already_exist()
    {
        $this->actingAs($this->user);
        $response = $this->putJson(
            $this->product_api_url.'/'.$this->product->id,
            [
                'title' => $this->existing_product->title,
                'price' => 888,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'title' => __('validation.unique', ['attribute' => 'title']),
            ]
        );
    }

    public function test_delete_product_successful()
    {
        $this->assertDatabaseHas('products', ['id' => $this->product->id]);
        $this->actingAs($this->user);
        $response = $this->deleteJson(
            $this->product_api_url.'/'.$this->product->id,
        );

        $response->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $this->product->id]);
    }

    public function test_delete_product_not_found()
    {
        $this->actingAs($this->user);
        $productId = $this->existing_product->id;
        $this->existing_product->delete();
        $response = $this->deleteJson(
            $this->product_api_url.'/'.$productId,
        );

        $response->assertNotFound();
    }

    public function test_unauthorized()
    {
        $response = $this->getJson($this->product_api_url);
        $response->assertUnauthorized();
        $response = $this->getJson($this->product_api_url.'/'.$this->product->id);
        $response->assertUnauthorized();
        $response = $this->postJson(
            $this->product_api_url,
            [
                'title' => Str::random(17),
                'price' => 789,
            ]
        );
        $response->assertUnauthorized();
        $response = $this->putJson(
            $this->product_api_url.'/'.$this->product->id,
            [
                'title' => Str::random(19),
                'price' => 789,
            ]
        );
        $response->assertUnauthorized();
        $response = $this->deleteJson($this->product_api_url.'/'.$this->product->id);
        $response->assertUnauthorized();
    }
}
