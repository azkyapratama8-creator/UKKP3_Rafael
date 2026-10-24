<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

// Route publik untuk autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk customer
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/pengaduan', [CustomerController::class, 'showPengaduan'])->name('pengaduan');
    Route::post('/pengaduan/submit', [CustomerController::class, 'submitPengaduan'])->name('pengaduan.submit');
});

// Route untuk admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengaduan', [AdminController::class, 'daftarPengaduan'])->name('pengaduan');
    Route::get('/pengaduan/{id}', [AdminController::class, 'detailPengaduan'])->name('pengaduan.detail');
});
