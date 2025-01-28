<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\UserController;
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
    Route::resource('courts', CourtController::class)->except(['create', 'edit']);
    Route::get('/cep/{cep}', [CourtController::class, 'findCep']);
    Route::get('/user/auth', function (Request $request) {
        return $request->user();
    });
    Route::get('owner/courts', [CourtController::class, 'getCourtsByOwner']);
    Route::put('users/{user}/approve', [UserController::class, 'changeApproveStatus']);


    Route::post('courts/favorite/{id}', [CourtController::class, 'favoriteCourt']);
    Route::get('favorites', [CourtController::class, 'getFavorites']);
});

Route::get('/sports', function() {
    return response()->json(Sport::all());
})->middleware('auth:sanctum');

Route::resource('users', UserController::class)->except(['create', 'edit']);
