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


Route::post('register', [App\Http\Controllers\Auth\UserController::class, 'register']);
Route::post('login', [App\Http\Controllers\Auth\UserController::class, 'login']);

Route::get('logout', [App\Http\Controllers\Auth\UserController::class, 'logout']);

Route::post('register/facebook', [App\Http\Controllers\Auth\SocialApiAuthFacebookController::class, 'facebookConnect']);
Route::post('register/twitter', [App\Http\Controllers\Auth\SocialApiAuthTwitterController::class, 'twitterConnect']);

Route::get('email/verify/{id}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name

Route::get('email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

Route::resource('vendeur', App\Http\Controllers\VendeurController::class);
Route::post('loginvendeur', [App\Http\Controllers\VendeurController::class, 'loginVendeur']);

Route::post('password/email', [App\Http\Controllers\Auth\UserController::class, 'forgot']);

Route::post('password/reset', [App\Http\Controllers\Auth\UserController::class, 'reset']);

Route::middleware('auth:api')->group(function () {
    Route::resource('event', App\Http\Controllers\EventController::class);
    Route::resource('package', App\Http\Controllers\PackageController::class);
    Route::resource('client', App\Http\Controllers\ClientController::class);
    Route::resource('forfait', App\Http\Controllers\ForfaitController::class);


    Route::post('revoque', [App\Http\Controllers\ClientController::class, 'revoque']);
    Route::post('getclient', [App\Http\Controllers\ClientController::class, 'getClient']);
    Route::post('getpackage', [App\Http\Controllers\PackageController::class, 'getPackage']);
    Route::post('getvendeur', [App\Http\Controllers\VendeurController::class, 'getVendeur']);

    Route::post('deletevendeur', [App\Http\Controllers\VendeurController::class, 'deleteVendeur']);
    Route::post('deletepackage', [App\Http\Controllers\PackageController::class, 'deletePackage']);
    Route::post('deleteevent', [App\Http\Controllers\EventController::class, 'deleteEvent']);
    Route::post('deleteforfait', [App\Http\Controllers\ForfaitController::class, 'deleteForfait']);

    Route::post('updateevent', [App\Http\Controllers\EventController::class, 'updateEvent']);
    Route::post('updatepackage', [App\Http\Controllers\PackageController::class, 'updatePackage']);
    Route::post('updateforfait', [App\Http\Controllers\ForfaitController::class, 'updateForfait']);
    Route::post('updatevues', [App\Http\Controllers\EventController::class, 'updateVues']);

    Route::post('updateuser', [App\Http\Controllers\Auth\UserController::class, 'updateUser']);
    Route::get('getuser', [App\Http\Controllers\Auth\UserController::class, 'getUser']);

    Route::post('showpackage', [App\Http\Controllers\PackageController::class, 'showPackage']);
    Route::post('showclient', [App\Http\Controllers\ClientController::class, 'showClient']);
    Route::post('showevent', [App\Http\Controllers\EventController::class, 'showEvent']);
    Route::post('allevent', [App\Http\Controllers\EventController::class, 'allEvent']);

    Route::get('pdfclient', [App\Http\Controllers\ClientController::class, 'printPdf']);

    Route::post('statistique', [App\Http\Controllers\StatistiqueController::class, 'statByEvent']);

    Route::post('buyforfait', [App\Http\Controllers\ForfaitController::class, 'buyForfait']);
});
