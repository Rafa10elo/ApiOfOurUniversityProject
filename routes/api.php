<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EditProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;


/*
 Public Routes
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/{id}', [ApartmentController::class, 'show']);

/*
| Authenticated Routes
*/
Route::middleware(['jwt.auth'])->group(function () {

    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::post('/profile/image', [EditProfileController::class, 'updateProfileImage']);
    Route::post('/profile/password', [EditProfileController::class, 'updatePassword']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});

/*
Owner Routes
*/
Route::middleware(['jwt.auth', 'role:owner'])->group(function () {

    Route::post('/apartments', [ApartmentController::class, 'store']);
    Route::put('/apartments/{id}', [ApartmentController::class, 'update']);
    Route::delete('/apartments/{id}', [ApartmentController::class, 'destroy']);

    Route::post('/bookings/{id}/approve', [BookingController::class, 'approve']);
    Route::post('/bookings/{id}/reject', [BookingController::class, 'reject']);
});

/*
Renter Routes
*/
Route::middleware(['jwt.auth', 'role:renter'])->group(function () {

    Route::post('/bookings/{apartmentId}', [BookingController::class, 'store']);
});

/*
Admin Routes
*/
Route::prefix('admin')->middleware(['jwt.auth', 'role:admin'])->group(function () {

    Route::get('/users/pending', [AdminController::class,'pendingUsers']);
    Route::post('/users/{id}/verify', [AdminController::class,'verifyUser']);
    Route::post('/users/{id}/reject', [AdminController::class,'rejectUser']);
});
