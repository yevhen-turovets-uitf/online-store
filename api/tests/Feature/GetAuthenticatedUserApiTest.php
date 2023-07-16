<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetAuthenticatedUserApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $get_auth_user_api_url;

    private mixed $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->get_auth_user_api_url = route('get-authenticated-user');

        $this->user = User::factory()->create(
            [
                'email' => 'featuretest@example.com',
            ]
        );
    }

    public function test_get_authenticated_user_successful()
    {
        $this->actingAs($this->user);
        $response = $this->getJson($this->get_auth_user_api_url);

        $response->assertOk()->assertJsonFragment(
                [
                    'name' => $this->user->getName(),
                    'email' => $this->user->getEmail(),
                ]
            )->assertJsonStructure(
                [
                    'id',
                    'name',
                    'email',
                ]
            );
    }

    public function test_user_unauthorized()
    {
        $response = $this->getJson($this->get_auth_user_api_url);

        $response->assertUnauthorized()->assertJsonFragment(
            [
                'error' => __('auth.unauthorized'),
            ]
        );
    }

}
