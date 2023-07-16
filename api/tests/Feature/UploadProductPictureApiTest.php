<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UploadProductPictureApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $api_url;

    private UploadedFile $image;
    private mixed $user;
    private mixed $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create(['picture' => null]);
        $this->api_url = route('upload-product-picture', ['product' => $this->product->id]);
        $this->image = UploadedFile::fake()->create('toy-picture', 3072, 'image/jpeg');
        $this->user = User::factory()->create(
            [
                'password' => Hash::make('TestPass1'),
                'email' => 'featuretest@example.com',
            ]
        );
    }

    public function test_upload_product_picture_success()
    {
        $this->actingAs($this->user);
        $response = $this->postJson($this->api_url, ['image' => $this->image]);

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'id',
                    'title',
                    'slug',
                    'picture',
                    'price',
                    'discountPrice',
                    'currency',
                    'qty',
                    'variants',
                ]
            );
    }

    public function test_upload_product_picture_check_required_fields()
    {
        $this->actingAs($this->user);

        $response = $this->postJson($this->api_url);

        $response->assertUnprocessable()->assertInvalid(
            [
                'image' => __('validation.required', ['attribute' => 'image']),
            ]
        );
    }

    public function test_upload_product_picture_max_file_size()
    {
        $this->actingAs($this->user);

        $data = ['image' => UploadedFile::fake()->create('huge-file', 20000, 'image/png')];
        $response = $this->postJson($this->api_url, $data);

        $response->assertUnprocessable()->assertInvalid(
            [
                'image' => __('validation.max.file', ['attribute' => 'image', 'max' => '10240']),
            ]
        );
    }

    public function test_upload_product_picture_incorrect_ext_file()
    {
        $this->actingAs($this->user);
        $data = ['image' => UploadedFile::fake()->create('wrong-ext', 1000, 'application/pdf')];
        $response = $this->postJson($this->api_url, $data);

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'image' => [
                    __('validation.image', ['attribute' => 'image']),
                    __('validation.mimetypes', ['attribute' => 'image', 'values' => 'jpg, jpeg, png']),
                ],
            ]
        );
    }


    public function test_unauthorized()
    {
        $response = $this->postJson($this->api_url);
        $response->assertUnauthorized();
    }
}
