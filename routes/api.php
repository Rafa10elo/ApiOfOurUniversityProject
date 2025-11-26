<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;




Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);


Route::middleware(['jwt.auth'])->group(function () {
    //this one is for testing guys
    Route::get('/auth/me', [AuthController::class, 'me']);
});



Route::prefix('admin')->middleware(['jwt.auth','admin'])->group(function () {

    Route::get('/users/pending', [AdminController::class,'pendingUsers']);



    Route::post('/users/{id}/verify', [AdminController::class,'verifyUser']);
    Route::post('/users/{id}/reject', [AdminController::class,'rejectUser']);

});
