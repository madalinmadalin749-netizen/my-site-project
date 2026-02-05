<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    | Dashboard
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    | Tests
    */
    Route::prefix('tests')->name('tests.')->group(function () {

        Route::get('/', [TestController::class, 'index'])->name('index');
        Route::post('/start', [TestController::class, 'start'])->name('start');

        Route::get('/history', [TestController::class, 'history'])->name('history');

        Route::get('/{attempt}', [TestController::class, 'show'])->name('show');
        Route::post('/{attempt}/answer', [TestController::class, 'answer'])->name('answer');
        Route::post('/{attempt}/submit', [TestController::class, 'submit'])->name('submit');

        Route::get('/{attempt}/result', [TestController::class, 'result'])->name('result');

        // AI
        Route::post('/{attempt}/ai', [TestController::class, 'aiGenerate'])
            ->name('aiGenerate');
    });
});

/*
|--------------------------------------------------------------------------
| Auth scaffolding (Breeze / Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
