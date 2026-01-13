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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
