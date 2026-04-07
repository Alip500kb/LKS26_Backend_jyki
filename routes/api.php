<?php

use App\Http\Controllers\AuthLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('/v1')->group(function () {
    //route api untuk Auth dengan controller AuthLogin
    Route::prefix('auth')->group(function () {
        Route::post('/signup', [AuthLogin::class, 'signup'])->middleware('guest');
        Route::post('/login', [AuthLogin::class, 'login'])->middleware('guest');
        Route::post('/signout', [AuthLogin::class, 'signout'])->middleware('auth:sanctum');
    });
});
