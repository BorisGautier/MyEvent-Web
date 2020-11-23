<?php

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

Route::post('register', [App\Http\Controllers\Auth\UserController::class, 'register']);
Route::post('login', [App\Http\Controllers\Auth\UserController::class, 'login']);

Route::post('register/facebook', [App\Http\Controllers\Auth\SocialApiAuthFacebookController::class, 'facebookConnect']);
Route::post('register/twitter', [App\Http\Controllers\Auth\SocialApiAuthTwitterController::class, 'twitterConnect']);

Route::get('email/verify/{id}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name

Route::get('email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

Route::middleware('auth:api')->group(function () {
    Route::resource('event', App\Http\Controllers\EventController::class);
});
