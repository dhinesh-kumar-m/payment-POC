<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\XeroController;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\UserController;

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

// Route::get('/', function () {
//     return view('welcome');
// });



Route::post('signup', [UserController::class, 'signup_user']);
Route::get('hello', [XeroController::class, 'redirectUserToXero']);
Route::get('hello/callback', [XeroController::class, 'handleCallbackFromXero']);


Route::group(['middleware' => ['api']], function () {
    Route::post('login', [JWTController::class, 'login']);
    Route::post('logout', [JWTController::class, 'logout']);
    Route::post('refresh',  [JWTController::class, 'refresh']);
    Route::post('me', [JWTController::class, 'me']);
});

Route::group(['middleware' => ['JwtMiddleWare']], function () {
    Route::get('test', [XeroController::class, 'testapi']);
    Route::get('create_contact', [XeroController::class, 'create_contact']);
    Route::get('create_invoice', [XeroController::class, 'create_invoice']);
    Route::get('hello/refresh', [XeroController::class, 'refreshAccessTokenIfNecessary']);

    // Route::get('hello/callback', [XeroController::class, 'handleCallbackFromXero']);

});



