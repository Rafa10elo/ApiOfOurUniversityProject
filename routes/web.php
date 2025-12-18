<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminViewController;
use Illuminate\Support\Facades\Route;

/*
Admin Routes
*/

Route::prefix('admin')->group(function () {

    Route::get('/pending-users', [AdminViewController::class, 'pendingUsers'])
        ->name('admin.pending');

    Route::get('/verified-users', [AdminViewController::class, 'verifiedUsers'])
        ->name('admin.verified');

    Route::get('/rejected-users', [AdminViewController::class, 'rejectedUsers'])
        ->name('admin.rejected');

    Route::put('/verify/{id}', [AdminController::class, 'verifyUser'])
        ->name('admin.verify');

    Route::put('/reject/{id}', [AdminController::class, 'rejectUser'])
        ->name('admin.reject');

    Route::delete('/remove/{id}', [AdminController::class, 'removeUser'])
        ->name('admin.remove');

});
