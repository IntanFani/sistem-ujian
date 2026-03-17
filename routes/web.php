<?php

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () { return redirect('/login');});

// Route untuk Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group Route yang butuh login
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Admin
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    //Route untuk manajemen mapel
    Route::get('/admin/subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
    Route::post('/admin/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
    Route::put('/admin/subjects/{id}', [SubjectController::class, 'update'])->name('admin.subjects.update');
    Route::delete('/admin/subjects/{id}', [SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

    //Route untuk manajemen guru
    Route::resource('/admin/gurus', GuruController::class)->names('admin.gurus');
    Route::put('/admin/gurus/{id}/reset-password', [GuruController::class, 'resetPassword'])->name('admin.gurus.reset');

    //Route untuk manajemen kelas
    Route::resource('/admin/kelas', App\Http\Controllers\Admin\KelasController::class)->names('admin.kelas');

    //Route untuk manajemen siswa
    Route::resource('/admin/siswas', App\Http\Controllers\Admin\SiswaController::class)->names('admin.siswas');
});