<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $auth_api_url;
    private string $refresh_api_url;
    private string $logout_api_url;
    private mixed $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->auth_api_url = route('auth.login');
        $this->refresh_api_url = route('auth.refresh');
        $this->logout_api_url = route('auth.logout');

        $this->user = User::factory()->create(
            [
                'password' => Hash::make('Testpass1'),
                'email' => 'featuretest@example.com',
            ]
        );
    }

    public function test_auth_user_successful()
    {
        $response = $this->postJson(
            $this->auth_api_url,
            [
                'email' => $this->user->email,
                'password' => 'Testpass1',
            ]
        );

        $response->assertOk()->assertJsonStructure(
                [
                    'user',
                    'token',
                    'type',
                    'time',
                ]
            );

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_auth_user_with_wrong_email()
    {
        $response = $this->postJson(
            $this->auth_api_url,
            [
                'email' => $this->faker->unique()->freeEmail(),
                'password' => 'Testpass1',
            ]
        );

        $response
            ->assertStatus(400)
            ->assertJsonFragment(
                [
                    'message' => __('auth.auth_error'),
                ]
            );
    }

    public function test_auth_user_with_wrong_password()
    {
        $response = $this->postJson(
            $this->auth_api_url,
            [
                'email' => $this->user->email,
                'password' => 'Wrong1pass',
            ]
        );

        $response->assertStatus(400)->assertJsonValidationErrors(
            [
                'message' => __('auth.auth_error'),
            ]
        );
    }

    public function test_refresh_token()
    {
        $this->postJson(
            $this->auth_api_url,
            [
                'email' => $this->user->email,
                'password' => 'Testpass1',
            ]
        );

        $response = $this->postJson($this->refresh_api_url);

        $response->assertOk()->assertJsonStructure(
            [
                'user',
                'token',
                'type',
                'time',
            ]
        );
    }

    public function test_logout()
    {
        $this->postJson(
            $this->auth_api_url,
            [
                'email' => $this->user->email,
                'password' => 'Testpass1',
            ]
        );
        $response = $this->postJson($this->logout_api_url);

        $response->assertNoContent();
    }
}
