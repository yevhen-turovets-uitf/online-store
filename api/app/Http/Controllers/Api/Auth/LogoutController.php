<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Auth;

class LogoutController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/auth/logout",
     *      summary="Logout",
     *      description="Logout user and invalidate token",
     *      operationId="authLogout",
     *      tags={"Auth"},
     *      security={ {"bearerAuth": {} }},
     *      @OA\Response(
     *          response=204,
     *          description="Success"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Returns when user is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The user is not authorized. Log in to perform the action."),
     *          )
     *      )
     * )
     */
    public function __invoke()
    {
        Auth::logout();

        return $this->emptyResponse();
    }
}
