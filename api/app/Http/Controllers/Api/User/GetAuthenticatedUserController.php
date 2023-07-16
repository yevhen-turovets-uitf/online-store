<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\UserArrayPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GetAuthenticatedUserController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/user",
     *      summary="Get Auth User",
     *      description="Return authorized user data",
     *      operationId="getAuthUser",
     *      tags={"Auth"},
     *      security={ {"bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/UserResponse"),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Returns when user is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The user is not authorized. Log in to perform the action."),
     *          )
     *      )
     * )
     *
     * @param  UserArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function __invoke(UserArrayPresenter $presenter): JsonResponse
    {
        $user = Auth::user();

        return $this->successResponse($presenter->present($user));
    }
}
