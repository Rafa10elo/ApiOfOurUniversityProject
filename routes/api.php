<?php

use App\Http\Controllers\AdminViewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EditProfileController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;

//reverb weow weow
Route::post('/broadcasting/auth', function (Illuminate\Http\Request $request) {

    $user = auth('api')->user();

    if (! $user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    return Broadcast::auth($request);
})->middleware('jwt.auth');

/*
 Public Routes
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/top-rated', [ApartmentController::class, 'topRated']);
Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
Route::get('/apartments/{apartmentId}/reviews', [ReviewController::class, 'index']);
 Route::get('/apartments/{id}/calendar',[BookingController::class,'apartmentCalendar']);

/*
Authenticated Routes
*/
Route::middleware(['jwt.auth'])->group(function () {

    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::delete('/auth/delete-account', [AuthController::class, 'deleteAccount']);



    Route::post('/profile/image', [EditProfileController::class, 'updateProfileImage']);
    Route::post('/profile/password', [EditProfileController::class, 'updatePassword']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::post('/favorites/{apartmentId}', [FavoriteController::class, 'toggle']);
    Route::get('/favorites', [FavoriteController::class, 'myFavorites']);
    Route::get('/favorites/{apartmentId}/status', [FavoriteController::class, 'isFavorited']);

    Route::post('/apartments/{apartmentId}/reviews', [ReviewController::class, 'store']);

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
    Route::get('/bookings/pending',[BookingController::class,'ownerPending']);
    Route::get('/bookings/approved',[BookingController::class,'ownerApproved']);
   Route::get('/bookings/cancelled',[BookingController::class,'ownerCancelled']);
   Route::get('/bookings/past',[BookingController::class,'ownerPast']);
});

/*
Renter Routes
*/
Route::middleware(['jwt.auth', 'role:renter'])->prefix("my")->group(function () {
    Route::post('/bookings/{apartmentId}', [BookingController::class, 'store']);
    Route::get('/bookings/pending',[BookingController::class,'renterPending']);
    Route::get('/bookings/approved',[BookingController::class,'renterApproved']);
   Route::get('/bookings/cancelled',[BookingController::class,'renterCancelled']);
    Route::get('/bookings/past',[BookingController::class,'renterPast']);
});


Route::get('/governorates', function () {
return response()->json( [
    'Damascus',
    'Rif Dimashq',
    'Aleppo',
    'Homs',
    'Hama',
    'Latakia',
    'Tartus',
    'Idlib',
    'Deir ez-Zor',
    'Raqqa',
    'Hasakah',
    'Daraa',
    'As-Suwayda',
    'Quneitra',
]);

});
