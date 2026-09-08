<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Simple auth routes - extend with Breeze/Jetstream for production
Route::get('/login', fn () => view('auth.login'))->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');
Route::get('/register', fn () => view('auth.register'))->name('register')->middleware('guest');
