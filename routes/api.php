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

use App\Http\Controllers\Api\V1\MeApiV1Controller;
use App\Http\Controllers\Api\V1\ProjectsApiV1Controller;
use App\Http\Controllers\Api\V1\TrackingsApiV1Controller;
use App\Http\Controllers\Api\V1\UsersApiV1Controller;
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

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Current user profile
    Route::get('/me', [MeApiV1Controller::class, 'show']);
    Route::put('/me', [MeApiV1Controller::class, 'update']);

    // Orion resource routes
    Orion::resource('projects', ProjectsApiV1Controller::class);
    Orion::resource('trackings', TrackingsApiV1Controller::class);
    Orion::resource('users', UsersApiV1Controller::class);
});
