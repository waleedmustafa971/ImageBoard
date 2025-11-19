<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

// Board Index
Route::get('/', [BoardController::class, 'index'])->name('boards.index');

// Board Routes
Route::get('/{board}', [BoardController::class, 'show'])->name('boards.show');
Route::get('/{board}/catalog', [BoardController::class, 'catalog'])->name('boards.catalog');

// Thread Routes
Route::get('/{board}/thread/create', [ThreadController::class, 'create'])->name('threads.create');
Route::post('/{board}/thread', [ThreadController::class, 'store'])->name('threads.store');
Route::get('/{board}/thread/{thread}', [ThreadController::class, 'show'])->name('threads.show');

// Post (Reply) Routes
Route::post('/{board}/thread/{thread}/reply', [PostController::class, 'store'])->name('posts.store');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Board Management
        Route::get('/boards', [AdminController::class, 'boardIndex'])->name('boards.index');
        Route::get('/boards/create', [AdminController::class, 'boardCreate'])->name('boards.create');
        Route::post('/boards', [AdminController::class, 'boardStore'])->name('boards.store');
        Route::get('/boards/{board}/edit', [AdminController::class, 'boardEdit'])->name('boards.edit');
        Route::put('/boards/{board}', [AdminController::class, 'boardUpdate'])->name('boards.update');
        Route::delete('/boards/{board}', [AdminController::class, 'boardDestroy'])->name('boards.destroy');

        // Thread Management
        Route::delete('/{board}/thread/{thread}', [AdminController::class, 'deleteThread'])->name('threads.delete');
        Route::post('/{board}/thread/{thread}/pin', [AdminController::class, 'togglePinThread'])->name('threads.pin');
        Route::post('/{board}/thread/{thread}/lock', [AdminController::class, 'toggleLockThread'])->name('threads.lock');

        // Post Management
        Route::delete('/{board}/thread/{thread}/post/{post}', [AdminController::class, 'deletePost'])->name('posts.delete');

        // Supervisor Management
        Route::get('/supervisors', [AdminController::class, 'supervisorIndex'])->name('supervisors.index');
        Route::get('/supervisors/create', [AdminController::class, 'supervisorCreate'])->name('supervisors.create');
        Route::post('/supervisors', [AdminController::class, 'supervisorStore'])->name('supervisors.store');
        Route::get('/supervisors/{supervisor}/edit', [AdminController::class, 'supervisorEdit'])->name('supervisors.edit');
        Route::put('/supervisors/{supervisor}', [AdminController::class, 'supervisorUpdate'])->name('supervisors.update');
        Route::delete('/supervisors/{supervisor}', [AdminController::class, 'supervisorDestroy'])->name('supervisors.destroy');

        // Activity Logs
        Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity.logs');
    });
});

// Supervisor Routes
Route::prefix('supervisor')->name('supervisor.')->group(function () {
    // Authentication
    Route::get('/login', [SupervisorController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SupervisorController::class, 'login']);
    Route::post('/logout', [SupervisorController::class, 'logout'])->name('logout');

    // Protected Supervisor Routes
    Route::middleware(['auth:supervisor'])->group(function () {
        Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

        // Thread Management (with board access check)
        Route::delete('/{board}/thread/{thread}', [SupervisorController::class, 'deleteThread'])->name('threads.delete')->middleware('supervisor.can_moderate');
        Route::post('/{board}/thread/{thread}/pin', [SupervisorController::class, 'togglePinThread'])->name('threads.pin')->middleware('supervisor.can_moderate');
        Route::post('/{board}/thread/{thread}/lock', [SupervisorController::class, 'toggleLockThread'])->name('threads.lock')->middleware('supervisor.can_moderate');

        // Post Management (with board access check)
        Route::delete('/{board}/thread/{thread}/post/{post}', [SupervisorController::class, 'deletePost'])->name('posts.delete')->middleware('supervisor.can_moderate');
    });
});
