<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\AuthUserResponseArrayPresenter;
use App\Http\Requests\Api\Auth\AuthValidationRequest;
use App\Http\Responses\Api\Auth\AuthenticationResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Sign in",
     *     description="Login by email, password",
     *     operationId="authLogin",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password1"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/AuthUserResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Wrong credentials response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email or password invalid.")
     *         )
     *     )
     * )
     */
    public function __invoke(
        AuthValidationRequest $request,
        AuthUserResponseArrayPresenter $presenter
    ): JsonResponse
    {
        $token = Auth::attempt([
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ]);

        if (!$token) {
            return $this->errorResponse(__('auth.auth_error'));
        }

        $user = Auth::user();

        $response = new AuthenticationResponse(
            $user,
            $token,
            'bearer',
            auth()->factory()->getTTL() * 60
        );

        return $this->successResponse($presenter->present($response));
    }
}
