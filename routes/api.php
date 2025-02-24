<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\UserController;
use App\Models\Court;
use App\Models\Sport;
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
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('api');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    
    //COURT
    Route::resource('courts', CourtController::class)->except(['create', 'edit', 'update']);
    Route::post('courts/{id}', [CourtController::class, 'update']);
    Route::get('owner/courts', [CourtController::class, 'getCourtsByOwner']);
    
    //FAVORITE
    Route::post('courts/favorite/{id}', [CourtController::class, 'favoriteCourt']);
    Route::get('favorites', [CourtController::class, 'getFavorites']);

    //BOOKINGS 
    Route::get('bookings', [CourtController::class, 'getBookingsBy']);
    Route::get('bookings/court/{courtId}', [CourtController::class, 'getBookings']);
    Route::post('court/book/{id}', [CourtController::class, 'book']);
    Route::put('approveBook/{bookingId}/{status}', [CourtController::class, 'approveBook']);
    Route::get('court/{courtId}/booking/{ownerId}', [CourtController::class, 'getBlockedDaysByOwner']);

    //VIACEP
    Route::get('/cep/{cep}', [CourtController::class, 'findCep']);

    //USER
    Route::put('users/{user}/approve', [UserController::class, 'changeApproveStatus']);
    Route::patch('reset-password', [UserController::class, 'updatePassword']);
    Route::get('/user/auth', function (Request $request) {
        return $request->user();
    });
});

Route::get('/sports', function() {
    return response()->json(Sport::all());
})->middleware('auth:sanctum');

Route::resource('users', UserController::class)->except(['create', 'edit']);
