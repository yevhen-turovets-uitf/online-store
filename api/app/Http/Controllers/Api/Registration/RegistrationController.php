<?php

namespace App\Http\Controllers\Api\Registration;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\AuthUserResponseArrayPresenter;
use App\Http\Requests\Api\Auth\RegisterValidationRequest;
use App\Http\Responses\Api\Auth\AuthenticationResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class RegistrationController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/auth/register",
     *      operationId="registerUser",
     *      tags={"Auth"},
     *      summary="Sing up",
     *      description="Register new user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\Property(
     *              property="name",
     *              type="string",
     *          ),
     *          @OA\Property(
     *              property="email",
     *              type="string",
     *              format="email",
     *          ),
     *          @OA\Property(
     *              property="password",
     *              type="string",
     *              format="password",
     *          ),
     *          @OA\Property(
     *              property="password_confirmation",
     *              type="string",
     *              format="password",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/AuthUserResponse")
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     * )
     */
    public function __invoke(
        RegisterValidationRequest $request,
        AuthUserResponseArrayPresenter $authUserResponseArrayPresenter
    ):JsonResponse
    {
        $user = User::create([
             'name' => $request->get('name'),
             'email' => $request->get('email'),
             'password' => Hash::make($request->get('password')),
        ]);

        $token = Auth::login($user);

        $response = new AuthenticationResponse(
            $user,
            (string)$token,
            'bearer',
            auth()->factory()->getTTL() * 60
        );

        return $this->successResponse($authUserResponseArrayPresenter->present($response));
    }
}
