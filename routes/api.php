<?php

use App\Http\Controllers\AppFlow;
use App\Http\Controllers\AuthLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//harus ubah tokenable_id type ke char di database
Route::prefix('/v1')->group(function () {
    //route api untuk Auth dengan controller AuthLogin
    Route::prefix('auth')->group(function () {
        Route::post('/signup', [AuthLogin::class, 'signup'])->middleware('guest');
        Route::post('/login', [AuthLogin::class, 'login'])->middleware(['guest','throttle:5.1']);
        Route::post('/signout', [AuthLogin::class, 'signout'])->middleware('auth:sanctum');
        Route::get('/info', [AuthLogin::class, 'info'])->middleware('auth:sanctum');
    });

    //Route untuk seputar verifikasi dan stagged request
    Route::post('/business-verifications', [AppFlow::class, 'verifikasi_bisnis'])->middleware('auth:sanctum');
    Route::patch('/business-verifications/{id}', [AppFlow::class, 'verifikasi_oleh_verifier'])->middleware('auth:sanctum');
    Route::get('/business-verifications', [AppFlow::class, 'cek_verif'])->middleware('auth:sanctum');
    //Route untuk pembiayaan
    Route::post('/financing-applications', [AppFlow::class, 'pengajuan_pembiayaan'])->middleware('auth:sanctum');
    Route::patch('/financing-applications/{id}', [AppFlow::class, 'analisis_peminjaman'])->middleware('auth:sanctum');
    Route::get('/financing-applications', [AppFlow::class, 'cek_verifs'])->middleware('auth:sanctum');

});
