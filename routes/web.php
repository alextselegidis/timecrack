<?php

/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\RecoveryController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TrackingsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    // WelcomeController
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

    // LoginController
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'perform'])->name('login.perform');

    // RecoveryController
    Route::get('/recovery', [RecoveryController::class, 'index'])->name('recovery');
    Route::post('/recovery', [RecoveryController::class, 'perform'])->name('recovery.perform');
});

// Auth routes
Route::middleware('auth')->group(function () {
    // LogoutController
    Route::post('/logout', [LogoutController::class, 'perform'])->name('logout.perform');

    // DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Timer actions
    Route::post('/timer/start/{project}', [DashboardController::class, 'start'])->name('timer.start');
    Route::post('/timer/stop', [DashboardController::class, 'stop'])->name('timer.stop');
    Route::post('/timer/pause', [DashboardController::class, 'togglePause'])->name('timer.pause');
    Route::post('/timer/discard', [DashboardController::class, 'discard'])->name('timer.discard');
    Route::post('/timer/message', [DashboardController::class, 'updateMessage'])->name('timer.message');
    Route::post('/projects/{project}/toggle-pin', [DashboardController::class, 'togglePin'])->name('projects.toggle-pin');

    // TrackingsController
    Route::resource('trackings', TrackingsController::class)->except(['show'])->names([
        'index' => 'trackings',
    ]);
    Route::get('/trackings/export/csv', [TrackingsController::class, 'export'])->name('trackings.export');

    // AccountController
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::post('/account/tokens', [AccountController::class, 'createToken'])->name('account.tokens.create');
    Route::delete('/account/tokens/{tokenId}', [AccountController::class, 'revokeToken'])->name('account.tokens.revoke');

    // AboutController
    Route::get('/about', [AboutController::class, 'index'])->name('about');

    // Setup routes (Admin only)
    Route::middleware(AdminMiddleware::class)->prefix('setup')->group(function () {
        // ProjectsController
        Route::resource('projects', ProjectsController::class)->except(['show'])->names([
            'index' => 'setup.projects',
            'create' => 'setup.projects.create',
            'store' => 'setup.projects.store',
            'edit' => 'setup.projects.edit',
            'update' => 'setup.projects.update',
            'destroy' => 'setup.projects.destroy',
        ]);

        // UsersController
        Route::resource('users', UsersController::class)->except(['show'])->names([
            'index' => 'setup.users',
            'create' => 'setup.users.create',
            'store' => 'setup.users.store',
            'edit' => 'setup.users.edit',
            'update' => 'setup.users.update',
            'destroy' => 'setup.users.destroy',
        ]);

        // SettingsController (Localization)
        Route::get('/localization', [SettingsController::class, 'index'])->name('setup.localization');
        Route::put('/localization', [SettingsController::class, 'update'])->name('setup.localization.update');
    });
});
