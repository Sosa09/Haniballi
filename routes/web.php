<?php

use App\Http\Controllers\WebRtcSignalingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::get('/appointments', fn () => view('appointments.index'))->name('appointments.index');
    Route::get('/training', fn () => view('training.index'))->name('training.index');
    Route::get('/nutrition', fn () => view('nutrition.index'))->name('nutrition.index');
    Route::get('/video/{appointmentId}', fn (int $appointmentId) => view('video.room', compact('appointmentId')))->name('video.room');

    Route::prefix('video/{appointmentId}')->group(function (): void {
        Route::post('/signal', [WebRtcSignalingController::class, 'postSignal'])->name('video.signal.post');
        Route::get('/signals', [WebRtcSignalingController::class, 'getSignals'])->name('video.signal.get');
        Route::post('/notes', [WebRtcSignalingController::class, 'saveNotes'])->name('video.notes.save');
        Route::post('/leave', [WebRtcSignalingController::class, 'leaveRoom'])->name('video.leave');
    });
});

require __DIR__.'/auth.php';
