<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [BookingController::class, 'dashboard'])->name('dashboard');

// Rooms management
Route::resource('rooms', RoomController::class);
Route::post('/rooms/check-availability', [RoomController::class, 'checkAvailability'])
    ->name('rooms.check-availability');

// Bookings management
Route::resource('bookings', BookingController::class);
Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])
    ->name('bookings.update-status');

// Authentication (Laravel Breeze / Fortify)
require __DIR__.'/auth.php';
