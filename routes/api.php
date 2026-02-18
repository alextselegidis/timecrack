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

use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\ProjectsController;
use App\Http\Controllers\Api\TrackingsController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Current user profile
    Route::get('/me', [MeController::class, 'show']);
    Route::put('/me', [MeController::class, 'update']);

    // Orion resource routes
    Orion::resource('projects', ProjectsController::class);
    Orion::resource('trackings', TrackingsController::class);
    Orion::resource('users', UsersController::class);
});
