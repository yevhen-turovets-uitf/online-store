<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function (){
    Route::resource('register', RegistrationController::class)->only('index','store');
    Route::resource('login', LoginController::class)->only('index','store');
    Route::resource('forgot-password', ForgotPasswordController::class)->only('index','store');
    Route::get('reset-password/{token}/{email}', [ResetPasswordController::class,'index'])->name('reset.password.get');
    Route::post('reset-password', [ResetPasswordController::class,'store'])->name('reset.password.post');
    Route::get('logout', LogoutController::class)->middleware('auth')->name('logout');
});
Route::middleware('auth')->group(function (){
    Route::get('home', function (){ return view('pages.home');})->name('home');
});
