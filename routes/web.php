<?php

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

// Route untuk Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group Route yang butuh login
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

});