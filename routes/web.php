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
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/submission/{submission}', [AdminController::class, 'viewSubmission'])->name('submission-detail');
    Route::patch('/document/{document}/status', [AdminController::class, 'updateDocumentStatus'])->name('update-document-status');
    Route::get('/document/{document}/download', [AdminController::class, 'downloadDocument'])->name('download-document');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
