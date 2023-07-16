<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $register_api_url;
    private string $name;
    private string $email;
    private string $password;

    private mixed $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->register_api_url = route('auth.register');
        $this->name = $this->faker->name();
        $this->email = $this->faker->unique()->safeEmail();
        $this->password = 'TestPass1';

        $this->user = User::factory()->create(
            [
                'password' => Hash::make('TestPass1'),
                'email' => 'featuretest@example.com',
            ]
        );
    }

    public function test_register_user_successful()
    {
        $this->assertDatabaseMissing('users', ['email' => $this->email]);

        $response = $this->postJson(
            $this->register_api_url,
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password,
            ]
        );

        $response->assertOk()->assertJsonFragment(
                [
                    'name' => $this->name,
                    'email' => $this->email,
                ]
            )->assertJsonStructure(
                [
                    'user',
                    'token',
                    'type',
                    'time',
                ]
            );

        $this->assertDatabaseHas('users', ['email' => $this->email]);
    }

    public function test_required_fields()
    {
        $response = $this->postJson(
            $this->register_api_url,
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                [
                    'name' => __('validation.required', ['attribute' => 'name']),
                    'email' => __('validation.required', ['attribute' => 'email']),
                    'password' => __('validation.required', ['attribute' => 'password']),
                ]
            );
    }

    public function test_incorrect_email()
    {
        $response = $this->postJson(
            $this->register_api_url,
            [
                'name' => $this->name,
                'email' => \Str::random(20),
                'password' => $this->password,
                'password_confirmation' => $this->password,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
                [
                    'email' => __('validation.email', ['attribute' => 'email']),
                ]
            );
    }

    public function test_user_already_register()
    {
        $response = $this->postJson(
            $this->register_api_url,
            [
                'email' => $this->user->email,
                'password' => $this->password,
                'password_confirmation' => $this->password,
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
                [
                    'email' => __('validation.unique', ['attribute' => 'email']),
                ]
            );
    }

    public function test_passwords_does_not_match()
    {
        $response = $this->postJson(
            $this->register_api_url,
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => 'Wrong1432',
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
                [
                    'password' => __('validation.confirmed', ['attribute' => 'password']),
                ]
            );
    }

    public function test_incorrect_password()
    {
        $response = $this->postJson(
            $this->register_api_url,
            [
                'email' => $this->email,
                'password' => 'sss',
                'password_confirmation' => 'sss',
            ]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(
                [
                    'password' => [
                        __('validation.min.string', ['attribute' => 'password', 'min' => '8']),
                        __('validation.password.mixed', ['attribute' => 'password']),
                        __('validation.password.numbers', ['attribute' => 'password']),
                    ],
                ]
            );
    }
}
