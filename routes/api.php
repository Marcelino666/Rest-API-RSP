<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider within a group which
// | is assigned the "api" middleware group. Enjoy building your API!
// |
// */

//Authentication
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;

//fitur
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Authentication Routes
Route::post('register', RegisterController::class);
Route::post('login', LoginController::class);
Route::post('logout', LogoutController::class);

Route::middleware(['auth'])->group(function () {
    Route::get('room', [RoomController::class, 'index']);
    Route::get('room/{rooms}', [RoomController::class, 'show']);

    Route::middleware(['auth.role:1'])->group(function () {
        Route::post('room', [RoomController::class, 'store']);
        Route::put('room/{rooms}', [RoomController::class, 'update']);
        Route::delete('room/{rooms}', [RoomController::class, 'delete']);
    });
});

//Fitur Routes
Route::resource('user', UserController::class)->except('create','store','edit','update');
Route::resource('booking', BookingController::class)->except('create','edit','update');
Route::post('checkin', CheckInController::class);
Route::post('checkout', CheckOutController::class);



