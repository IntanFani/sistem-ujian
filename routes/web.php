<?php

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Guru\ExamController;
use App\Http\Controllers\Siswa\ExamController as SiswaExamController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestion;
use App\Http\Controllers\Guru\QuestionController as GuruQuestion;

// Redirection awal
Route::get('/', function () { return redirect('/login'); });

// Route untuk Login (Terbuka untuk umum)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// SEMUA Route di bawah ini WAJIB login
Route::middleware(['auth'])->group(function () {
    
    // --- GROUP KHUSUS ADMIN ---
    // Tambahkan checkRole:admin agar Guru tidak bisa "nyasar" ke sini
    Route::middleware(['checkRole:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Mapel
        Route::resource('/subjects', SubjectController::class)->names('subjects');

        // Manajemen Guru
        Route::resource('/gurus', GuruController::class)->names('gurus');
        Route::put('/gurus/{id}/reset-password', [GuruController::class, 'resetPassword'])->name('gurus.reset');

        // Manajemen Kelas & Siswa
        Route::resource('/kelas', KelasController::class)->names('kelas');
        Route::resource('/siswas', SiswaController::class)->names('siswas');

        // Bank Soal Admin
        Route::resource('/questions', AdminQuestion::class)->names('questions');
    });

    // --- GROUP KHUSUS GURU ---
    Route::middleware(['checkRole:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');
        
        // Rute Bank Soal Khusus Guru
        Route::resource('/questions', GuruQuestion::class)->names('questions');

        // Route Manajemen ujian
        Route::resource('/exams', ExamController::class);
        Route::get('/exams/{id}/questions', [ExamController::class, 'manageQuestions'])->name('exams.questions');
        Route::post('/exams/{id}/questions', [ExamController::class, 'storeQuestions'])->name('exams.questions.store');
        Route::delete('/exams/{id}/questions/remove', [ExamController::class, 'removeQuestion'])->name('exams.questions.remove');
        Route::get('/exams/{id}/monitor', [ExamController::class, 'monitor'])->name('exams.monitor');
        Route::get('/results', [ExamController::class, 'results'])->name('results.index');
        Route::get('/results/{id}', [ExamController::class, 'showResult'])->name('results.show');
        Route::delete('/exams/sessions/{id}/reset', [ExamController::class, 'resetSession'])->name('exams.reset-session');
        Route::delete('/exams/{id}/reset-all', [ExamController::class, 'resetAllSessions'])->name('exams.reset-all');
    });

    // Group untuk Siswa
    Route::middleware(['auth', 'checkRole:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaExamController::class, 'index'])->name('dashboard');
        
        // Route untuk mulai ujian (Verifikasi Token)
        Route::post('/exams/{id}/start', [SiswaExamController::class, 'start'])->name('exams.start');
    
        // Route untuk halaman pengerjaan soal (Halaman Utama Ujian)
        Route::get('/exams/{id}/show', [SiswaExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/save-answer', [SiswaExamController::class, 'saveAnswer'])->name('exams.save-answer');
        Route::post('/exams/{id}/finish', [SiswaExamController::class, 'finish'])->name('exams.finish');
    });

});