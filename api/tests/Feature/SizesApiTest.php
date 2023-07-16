<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VariantSize;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SizesApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $sizes_api_url;
    private mixed $user;
    private mixed $existing_size;
    private mixed $size;

    public function setUp(): void
    {
        parent::setUp();

        $this->sizes_api_url = 'api/v1/sizes';

        $this->user = User::factory()->create(
            [
                'password' => Hash::make('TestPass1'),
            ]
        );
        $this->size = VariantSize::factory()->create(['name' => "MY"]);
        $this->existing_size = VariantSize::factory()->create(['name' => 'P']);
    }

    public function test_get_all_sizes_successful()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(
            $this->sizes_api_url
        );

        $response->assertOk()->assertJsonStructure(
            [
                '*' => [
                    'id',
                    'name',
                ],
            ]
        )->assertJsonCount(VariantSize::all()->count());
    }

    public function test_get_size_successful()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(
            $this->sizes_api_url.'/'.$this->size->id,
        );

        $response->assertOk()->assertJsonFragment(
            [
                'id' => $this->size->id,
                'name' => $this->size->name,
            ],
        );
    }

    public function test_size_not_found()
    {
        $this->actingAs($this->user);
        $response = $this->getJson(
            $this->sizes_api_url.'/999',
        );

        $response->assertNotFound();
    }

    public function test_create_size_successful()
    {
        $this->actingAs($this->user);
        $response = $this->postJson(
            $this->sizes_api_url,
            [
                'name' => 'XML',
            ]
        );

        $response->assertOk()->assertJsonFragment(
            [
                'name' => 'XML',
            ]
        );
    }

    public function test_create_size_already_exist()
    {
        $this->actingAs($this->user);
        $response = $this->postJson(
            $this->sizes_api_url,
            [
                'name' => $this->existing_size->name,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'name' => __('validation.unique', ['attribute' => 'name']),
            ]
        );
    }

    public function test_create_size_empty_request()
    {
        $this->actingAs($this->user);
        $response = $this->postJson(
            $this->sizes_api_url
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'name' => __('validation.required', ['attribute' => 'name']),
            ]
        );
    }

    public function test_edit_size_successful()
    {
        $this->actingAs($this->user);
        $response = $this->putJson(
            $this->sizes_api_url.'/'.$this->size->id,
            [
                'name' => 'BBQ',
            ]
        );

        $response->assertOk()->assertJsonFragment(
            [
                'id' => $this->size->id,
                'name' => 'BBQ',
            ]
        );
    }

    public function test_edit_size_name_already_exist()
    {
        $this->actingAs($this->user);
        $response = $this->putJson(
            $this->sizes_api_url.'/'.$this->size->id,
            [
                'name' => $this->existing_size->name,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
            [
                'name' => __('validation.unique', ['attribute' => 'name']),
            ]
        );
    }

    public function test_delete_size_successful()
    {
        $this->actingAs($this->user);
        $response = $this->deleteJson(
            $this->sizes_api_url.'/'.$this->size->id,
        );

        $response->assertNoContent();
        $this->assertDatabaseMissing('variant_sizes', ['id' => $this->size->id]);
    }

    public function test_delete_size_not_found()
    {
        $this->actingAs($this->user);
        $sizeId = $this->existing_size->id;
        $this->existing_size->delete();
        $response = $this->deleteJson(
            $this->sizes_api_url.'/'.$sizeId,
        );

        $response->assertNotFound();
    }

    public function test_unauthorized()
    {
        $response = $this->getJson($this->sizes_api_url);
        $response->assertUnauthorized();
        $response = $this->getJson($this->sizes_api_url.'/'.$this->size->id);
        $response->assertUnauthorized();
        $response = $this->postJson($this->sizes_api_url,['name' => 'YIO']);
        $response->assertUnauthorized();
        $response = $this->putJson($this->sizes_api_url.'/'.$this->size->id, ['name' => 'PUT']);
        $response->assertUnauthorized();
        $response = $this->deleteJson($this->sizes_api_url.'/'.$this->size->id);
        $response->assertUnauthorized();
    }
}
