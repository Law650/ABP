<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use App\Models\Event;

// ----------------------------------------------------
// PUBLIC ROUTES (Bisa diakses siapa saja tanpa login)
// ----------------------------------------------------
Route::get('/browse-events', [EventController::class, 'browse'])->name('events.browse');

// ----------------------------------------------------
// AUTHENTICATED ROUTES (Wajib login)
// ----------------------------------------------------
Route::middleware('auth')->group(function () {

    // Semua yang sudah login bisa lihat detail dan riwayat
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('/event-history', [EventController::class, 'showHistory'])->name('user.event.history');
    Route::get('/registration/{id}', [EventController::class, 'showRegistration'])->name('registration.show');

    // ----------------------------------------------------
    // KHUSUS EO & ADMIN (Manajemen Event & AI)
    // ----------------------------------------------------
    Route::middleware('role:eo,admin')->group(function () {
        
        // Form submit event (create)
        Route::get('/submit-event', [EventController::class, 'create'])->name('submit-event.form');
        
        // Simpan event baru
        Route::post('/submit-event', [EventController::class, 'store'])->name('submit-event');
        
        // Update event
        Route::put('/submit-event/{id}', [EventController::class, 'update'])->name('submit-event.update');
        
        // AI Gemini Flash API Endpoint
        Route::post('/generate-description', [EventController::class, 'generateDescription'])->name('event.generate-description');
    });

    // ----------------------------------------------------
    // NOTIFICATIONS (Tandai semua sebagai sudah dibaca)
    // ----------------------------------------------------    
    Route::post('/notifications/mark-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead')->middleware('auth');

    // ==========================================
    // FITUR AUTO EXPIRED EVENT SETIAP HARI
    // ==========================================
    Schedule::call(function () {
        
        // Ubah status event yang sudah lewat batas tanggalnya menjadi 'expired'
        Event::whereDate('end_date', '<', today())
            ->whereIn('status', ['approved', 'pending'])
            ->update(['status' => 'expired']);

    })->daily();    

});