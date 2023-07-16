<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RefreshAuthTokenController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Product\UploadProductPictureController;
use App\Http\Controllers\Api\ProductVariant\ProductVariantController;
use App\Http\Controllers\Api\Registration\RegistrationController;
use App\Http\Controllers\Api\User\GetAuthenticatedUserController;
use App\Http\Controllers\Api\Variant\VariantSizeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function (){
        Route::post('register', RegistrationController::class)->name('auth.register');
        Route::post('login', LoginController::class)->name('auth.login');
        Route::post('forgot-password', ForgotPasswordController::class)->name('auth.sent');
        Route::post('reset-password', ResetPasswordController::class)->name('auth.password');
        Route::post('refresh', RefreshAuthTokenController::class)->middleware('auth:api')->name('auth.refresh');
        Route::post('logout', LogoutController::class)->middleware('auth:api')->name('auth.logout');
    });
    Route::middleware('auth:api')->group(function (){
      Route::get('user', GetAuthenticatedUserController::class)->name('get-authenticated-user');
    });
    Route::apiResource('products', ProductController::class);
    Route::post('products/{product}/picture', UploadProductPictureController::class)->name('upload-product-picture');
    Route::apiResource('products.variants', ProductVariantController::class)->shallow();
    Route::apiResource('sizes', VariantSizeController::class);
});
