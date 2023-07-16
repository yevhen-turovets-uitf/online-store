<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\FailedSentPasswordResetLinkException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\SendResetPasswordLinkValidationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/auth/forgot-password",
     *     summary="Send Reset Link",
     *     description="Send reset link on email",
     *     operationId="authSent",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *             ),
     *         ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="We have emailed your password reset link!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Link not sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Email could not be sent to this email address.")
     *         )
     *     ),
     * )
     */
    public function __invoke(
        SendResetPasswordLinkValidationRequest $request
    ): JsonResponse
    {
        $response = Password::broker()->sendResetLink(['email' => $request->get('email')]);

        if ($response != Password::RESET_LINK_SENT){
            throw new FailedSentPasswordResetLinkException();
        }

        return $this->successResponse(['message' => __('passwords.sent')]);
    }
}
