<?php

declare(strict_types=1);

namespace App\Http\Presenters;

use App\Http\Responses\Api\Auth\AuthenticationResponse;

/**
 * @OA\Schema(
 *      schema="AuthUserResponse",
 *      description="Returns auth user array response",
 *      type="object",
 *      @OA\Property(property="user", type="object", ref="#/components/schemas/UserResponse"),
 *      @OA\Property(property="token", type="string", description="Access token", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE2NzA2ODQ3NTEsImV4cCI6MTY3MzI3Njc1MSwibmJmIjoxNjcwNjg0NzUxLCJqdGkiOiJtZ1pUMmh0MVlESHEzdTZ0Iiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.YKQpm0Ykhx1xvkAsYnGLnkjN8LJ0KTEsfs37dtHMrmM"),
 *      @OA\Property(property="type", type="string", description="Type of access token", example="brearer"),
 *      @OA\Property(property="time", type="integer", format="int64", description="expired in X of seconds", example="2592000"),
 * )
 */

final class AuthUserResponseArrayPresenter
{
    public function __construct(private UserArrayPresenter $userArrayPresenter)
    {}

    public function present(AuthenticationResponse $response): array
    {
        return [
            'user' => $this->userArrayPresenter->present($response->getUser()),
            'token' => $response->getAccessToken(),
            'type' => $response->getTokenType(),
            'time' => $response->getExpiresIn()
        ];
    }
}
