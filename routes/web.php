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
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Siswa\ExamController as SiswaExamController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;



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
        Route::post('/siswas/naik-kelas', [SiswaController::class, 'prosesNaikKelas'])->name('siswas.naik-kelas');
        Route::post('/siswas/import', [SiswaController::class, 'importExcel'])->name('siswas.import');
        Route::resource('/siswas', SiswaController::class)->names('siswas');

       // Manajemen Ujian oleh Admin
        Route::get('/exams', [AdminExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/create', [AdminExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [AdminExamController::class, 'store'])->name('exams.store'); 
        Route::get('/exams/{id}/edit', [AdminExamController::class, 'edit'])->name('exams.edit'); 
        Route::put('/exams/{id}', [AdminExamController::class, 'update'])->name('exams.update'); 
        Route::delete('/exams/{id}', [AdminExamController::class, 'destroy'])->name('exams.destroy'); 

        // Route Kelola Soal oleh Admin
        Route::get('/exams/{id}/questions', [AdminExamController::class, 'questions'])->name('exams.questions');
        Route::post('/exams/{id}/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
        Route::put('/exams/{id}/questions/{question_id}', [AdminExamController::class, 'updateQuestion'])->name('exams.questions.update');
        Route::delete('/exams/{id}/questions/{question_id}', [AdminExamController::class, 'destroyQuestion'])->name('exams.questions.destroy');
        Route::post('/exams/{id}/questions/import', [AdminExamController::class, 'importQuestions'])->name('exams.questions.import');

        // Tombol Aksi Cepat
        Route::post('/exams/{id}/toggle-status', [AdminExamController::class, 'toggleStatus'])->name('exams.toggle-status');
        Route::post('/exams/{id}/generate-token', [AdminExamController::class, 'generateToken'])->name('exams.generate-token');

        // Monitoring Ujian
        Route::get('/exams/{id}/monitor', [AdminExamController::class, 'monitor'])->name('exams.monitor');
        Route::post('/exams/sessions/{id}/reset', [AdminExamController::class, 'resetSession'])->name('exams.reset-session');
        Route::post('/exams/{id}/reset-all-sessions', [AdminExamController::class, 'resetAllSessions'])->name('exams.reset-all-sessions');
        
        // Kelola Soal (Admin bisa kelola soal juga)
        Route::get('/exams/{id}/questions', [AdminExamController::class, 'questions'])->name('exams.questions'); 
    
        // Report Nilai
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{id}/export-excel', [AdminReportController::class, 'exportExcel'])->name('reports.export-excel');
    });



    // --- GROUP KHUSUS GURU ---
    Route::middleware(['checkRole:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');

    // Route Manajemen ujian (Resource otomatis: index, create, store, edit, update, destroy)
    Route::resource('/exams', ExamController::class);

    // Custom Routes untuk Kelola Soal (Gunakan exam_id agar lebih jelas)
    Route::get('/exams/{exam_id}/questions', [ExamController::class, 'manageQuestions'])->name('exams.questions');
    Route::post('/exams/{exam_id}/questions', [ExamController::class, 'storeQuestions'])->name('exams.questions.store');
    Route::delete('/exams/questions/{question_id}/remove', [ExamController::class, 'removeQuestion'])->name('exams.questions.remove');
    Route::put('/exams/{id}/questions/{question_id}', [ExamController::class, 'updateQuestion'])->name('exams.questions.update');
    Route::post('/exams/{id}/questions/import', [ExamController::class, 'importQuestions'])->name('exams.questions.import');

    // Manajemen Hasil
    Route::get('/results', [ExamController::class, 'results'])->name('results.index');
    Route::get('/results/{id}', [ExamController::class, 'showResult'])->name('results.show');
    Route::get('/results/{id}/analysis', [ExamController::class, 'analysis'])->name('results.analysis');

    // Reset & Export
    Route::delete('/exams/sessions/{id}/reset', [ExamController::class, 'resetSession'])->name('exams.reset-session');
    Route::delete('/exams/{id}/reset-all', [ExamController::class, 'resetAllSessions'])->name('exams.reset-all');
    Route::get('/exams/{id}/export-excel', [ExamController::class, 'exportExcel'])->name('exams.export-excel');
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

        // Route untuk halaman riwayat ujian
        Route::get('/riwayat', [SiswaExamController::class, 'riwayat'])->name('riwayat');

    });


});