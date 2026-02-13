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

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecoveryController extends Controller
{
    public function index()
    {
        return view('pages.recovery');
    }

    public function perform(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Password reset logic would go here
        // For now, just redirect with a message

        return redirect()->route('login')->with('success', __('If your email exists, you will receive a password reset link.'));
    }
}
