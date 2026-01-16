<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengajuan-yudisium', [StudentController::class, 'pengajuanYudisium'])->name('pengajuan-yudisium');
    Route::post('/upload-document', [StudentController::class, 'uploadDocument'])->name('upload-document');
    Route::post('/submit-application', [StudentController::class, 'submitApplication'])->name('submit-application');
    Route::get('/articles', [StudentController::class, 'articles'])->name('articles');
    Route::get('/articles/{article}', [StudentController::class, 'showArticle'])->name('article-detail');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifikasi-pengajuan', [AdminController::class, 'verifikasiPengajuan'])->name('verifikasi-pengajuan');
    Route::get('/submission/{submission}', [AdminController::class, 'viewSubmission'])->name('submission-detail');
    Route::patch('/submission/{submission}/verification', [AdminController::class, 'updateVerification'])->name('update-verification');
    Route::patch('/document/{document}/status', [AdminController::class, 'updateDocumentStatus'])->name('update-document-status');
    Route::patch('/submission/{submission}/batch-update', [AdminController::class, 'batchUpdateDocuments'])->name('batch-update-documents');
    Route::get('/document/{document}/download', [AdminController::class, 'downloadDocument'])->name('download-document');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/import', [AdminController::class, 'showImportUsers'])->name('users.import');
    Route::post('/users/import', [AdminController::class, 'importUsers'])->name('users.import.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Article Management
    Route::get('/articles', [AdminController::class, 'articles'])->name('articles');
    Route::get('/articles/create', [AdminController::class, 'createArticle'])->name('articles.create');
    Route::post('/articles', [AdminController::class, 'storeArticle'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminController::class, 'editArticle'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminController::class, 'updateArticle'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminController::class, 'deleteArticle'])->name('articles.delete');

    // Periode Management
    Route::get('/periodes', [AdminController::class, 'periodes'])->name('periodes');
    Route::get('/periodes/create', [AdminController::class, 'createPeriode'])->name('periodes.create');
    Route::post('/periodes', [AdminController::class, 'storePeriode'])->name('periodes.store');
    Route::get('/periodes/{periode}/edit', [AdminController::class, 'editPeriode'])->name('periodes.edit');
    Route::put('/periodes/{periode}', [AdminController::class, 'updatePeriode'])->name('periodes.update');
    Route::delete('/periodes/{periode}', [AdminController::class, 'deletePeriode'])->name('periodes.delete');

    // Yudisium Siding Management
    Route::get('/yudisium-sidings', [AdminController::class, 'yudisiumSidings'])->name('yudisium-sidings');
    Route::get('/yudisium-sidings/create', [AdminController::class, 'createYudisiumSiding'])->name('yudisium-sidings.create');
    Route::post('/yudisium-sidings', [AdminController::class, 'storeYudisiumSiding'])->name('yudisium-sidings.store');
    Route::get('/yudisium-sidings/{yudisiumSiding}', [AdminController::class, 'showYudisiumSiding'])->name('yudisium-sidings.show');
    Route::get('/yudisium-sidings/{yudisiumSiding}/edit', [AdminController::class, 'editYudisiumSiding'])->name('yudisium-sidings.edit');
    Route::patch('/yudisium-sidings/{yudisiumSiding}', [AdminController::class, 'updateYudisiumSiding'])->name('yudisium-sidings.update');
    Route::delete('/yudisium-sidings/{yudisiumSiding}', [AdminController::class, 'deleteYudisiumSiding'])->name('yudisium-sidings.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
