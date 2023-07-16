<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\InvalidResetPasswordTokenException;
use App\Exceptions\ResetPasswordThrottledException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\ResetPasswordValidationRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/auth/reset-password",
     *     summary="Reset password",
     *     description="Reset user's password",
     *     operationId="passwordReset",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"token","email","password","password_confirmation"},
     *             @OA\Property(property="token", type="string", description="Password reset token", example="6fc3e2c8536ba926235d427ac0d729b59d82fc9d091037640d476189217097d7"),
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="NewPassword2"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassword2"),
     *             ),
     *         ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Your password has been reset!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid password reset token",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example={"This password reset token is invalid.","Please wait before retrying."},
     *                  ),
     *                  @OA\Property(property="code", type="string", example="400")
     *              )
     *         )
     *     ),
     * )
     */
    public function __invoke(
        ResetPasswordValidationRequest $request
    ): JsonResponse{
        $response = Password::broker()->reset(
            [
                'email' => $request->get('email'),
                'password' => $request->get('password'),
                'password_confirmation' => $request->get('password_confirmation'),
                'token' => $request->get('token'),
            ],
            function ($user, $password)
            {
                $user->forceFill(['password' => Hash::make($password)])->save();
                event(new PasswordReset($user));
            }
        );

        if ($response == Password::INVALID_TOKEN || $response != Password::PASSWORD_RESET)
        {
            throw new InvalidResetPasswordTokenException();
        }

        if ($response != Password::RESET_THROTTLED)
        {
            throw new ResetPasswordThrottledException();
        }

        return $this->successResponse(['message' => __('passwords.reset')]);
    }
}
