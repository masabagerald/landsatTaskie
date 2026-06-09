<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', fn () => redirect()->route('dashboard'));

/* Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
 */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('users', UserController::class);



Route::resource('tasks', TaskController::class);
Route::get('/tasks/{task}/start',    [TaskController::class, 'start'])->name('tasks.start');
Route::get('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

 Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

Route::resource('categories', CategoryController::class);

require __DIR__.'/auth.php';
