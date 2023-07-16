<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\AuthUserResponseArrayPresenter;
use App\Http\Responses\Api\Auth\AuthenticationResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RefreshAuthTokenController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Refresh",
     *     description="refresh auth token",
     *     operationId="authRefresh",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/AuthUserResponse")
     *     )
     * )
     */
    public function __invoke(
        AuthUserResponseArrayPresenter $presenter
    ): JsonResponse
    {
        $response = new AuthenticationResponse(
            Auth::user(),
            Auth::refresh(),
            'bearer',
            auth()->factory()->getTTL() * 60
        );

        return $this->successResponse($presenter->present($response));
    }
}
