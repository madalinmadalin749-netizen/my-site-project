<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TestController;

use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\QuestionAdminController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::view('/', 'landing')->name('landing');
Route::view('/pricing', 'pricing')->name('pricing');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tests
    Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
    Route::post('/tests/start', [TestController::class, 'start'])->name('tests.start');

    Route::get('/tests/{attempt}', [TestController::class, 'show'])->name('tests.show');
    Route::post('/tests/{attempt}/answer', [TestController::class, 'answer'])->name('tests.answer');
    Route::post('/tests/{attempt}/submit', [TestController::class, 'submit'])->name('tests.submit');
    Route::get('/tests/{attempt}/result', [TestController::class, 'result'])->name('tests.result');

    // Premium AI (mock)
    Route::post('/tests/{attempt}/ai-generate', [TestController::class, 'aiGenerate'])->name('tests.aiGenerate');

    // History
    Route::get('/history', [TestController::class, 'history'])->name('tests.history');
    Route::get('/history/{attempt}', [TestController::class, 'result'])->name('history.show');

    /*
    |--------------------------------------------------------------------------
    | Admin (auth + admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::resource('categories', CategoryAdminController::class)->except(['show']);
        Route::resource('questions', QuestionAdminController::class)->except(['show']);

        Route::post('questions/import', [QuestionAdminController::class, 'import'])->name('questions.import');
    });
});

require __DIR__ . '/auth.php';
