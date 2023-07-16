<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Password;
use Tests\TestCase;

class ResetPasswordApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $send_link_api_url;
    private string $reset_password_api_url;
    private string $token;
    private string $email;
    private string $password;

    private mixed $user;
    private mixed $user2;

    public function setUp(): void
    {
        parent::setUp();

        $this->send_link_api_url = route('auth.sent');
        $this->reset_password_api_url = route('auth.password');
        $this->email = $this->faker->unique()->email();
        $this->password = 'Testpass1';
        $this->token = $this->faker->sha1();

        $this->user = User::factory()->create([
            'password' => Hash::make('Testpass2'),
            'email'    => $this->email,
        ]);

        $this->user2 = User::factory()->create([
            'password' => Hash::make('Testpass2'),
            'email'    => fake()->unique()->email(),
        ]);
    }

    public function test_sent_reset_password_link_successful()
    {
        $this->assertDatabaseMissing(config('auth.passwords.users.table'),['email' => $this->email]);

        $response = $this->postJson($this->send_link_api_url, [
            'email' => $this->email,
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment(['message' => __('passwords.sent')]);

        $this->assertDatabaseHas(config('auth.passwords.users.table'),['email' => $this->email]);
    }

    public function test_incorrect_email()
    {
        $response = $this->postJson($this->send_link_api_url, [
            'email' => \Str::random(20),
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email' => __('validation.email', ['attribute' => 'email'])
            ]);
    }

    public function test_sent_empty_email()
    {
        $response = $this->postJson($this->send_link_api_url, [
            'email' => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email' => __('validation.required', ['attribute' => 'email'])
            ]);
    }

    public function test_user_does_not_exist()
    {
        $response = $this->postJson($this->send_link_api_url, [
            'email' => $this->faker->unique()->safeEmail(),
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email' => __('validation.exists', ['attribute' => 'email'])
            ]);
    }

    public function test_reset_passwords_successful()
    {
        $token = Password::createToken($this->user2);

        $response = $this->postJson($this->reset_password_api_url, [
            'email' => $this->user2->email,
            'token' => $token,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'message' => __('passwords.reset')
            ]);
    }

    public function test_new_passwords_does_not_match()
    {
        $token = Password::createToken($this->user2);

        $response = $this->postJson($this->reset_password_api_url, [
            'email' => $this->user2->email,
            'token' => $token,
            'password' => $this->password,
            'password_confirmation' => 'Wrong1432',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'password' => __('validation.confirmed', ['attribute' => 'password'])
            ]);
    }

    public function test_incorrect_new_password()
    {
        $token = Password::createToken($this->user2);

        $response = $this->postJson($this->reset_password_api_url, [
            'email' => $this->user2->email,
            'token' => $token,
            'password' => 'sss',
            'password_confirmation' => 'sss',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'password' => [
                    __('validation.min.string', ['attribute' => 'password', 'min' => '8']),
                    __('validation.password.mixed', ['attribute' => 'password']),
                    __('validation.password.numbers', ['attribute' => 'password']),
                ]
            ]);
    }

    public function test_check_require_fields_for_reset()
    {
        Password::createToken($this->user2);

        $response = $this->postJson($this->reset_password_api_url);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'token' => __('validation.required', ['attribute' => 'token']),
                'email' => __('validation.required', ['attribute' => 'email']),
                'password' => __('validation.required', ['attribute' => 'password']),
            ]);
    }

    public function test_reset_token_expired()
    {
        DB::table(config('auth.passwords.users.table'))->insert([
            'email' => $this->user2->email,
            'token' => $this->token,
            'created_at' => Carbon::now()->subSeconds(config('auth.passwords.users.expire')+1),
        ]);

        $response = $this->postJson($this->reset_password_api_url, [
            'email' => $this->user2->email,
            'token' => $this->token,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $response
            ->assertStatus(400)
            ->assertJsonValidationErrors([
                'message' => __('passwords.token')
            ]);
    }
}
