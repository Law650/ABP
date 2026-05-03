<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

// ----------------------------------------------------
// PUBLIC ROUTES (Bisa diakses siapa saja tanpa login)
// ----------------------------------------------------
Route::get('/browse-events', [EventController::class, 'browse'])->name('events.browse');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

// ----------------------------------------------------
// AUTHENTICATED ROUTES (Wajib login)
// ----------------------------------------------------
Route::middleware('auth')->group(function () {

    // Form submit event (create)
    Route::get('/submit-event', [EventController::class, 'create'])
        ->name('submit-event.form');

    // Simpan event baru
    Route::post('/submit-event', [EventController::class, 'store'])
        ->name('submit-event');

    // Update event
    Route::put('/submit-event/{id}', [EventController::class, 'update'])
        ->name('submit-event.update');

    // Riwayat event user
    Route::get('/event-history', [EventController::class, 'showHistory'])
        ->name('user.event.history');

    // Detail registrasi user
    Route::get('/registration/{id}', [EventController::class, 'showRegistration'])
        ->name('registration.show');

    // ----------------------------------------------------
    // FITUR BARU: Route untuk API AI Gemini Flash
    // ----------------------------------------------------
    Route::post('/generate-description', [EventController::class, 'generateDescription'])
        ->name('event.generate-description');

});