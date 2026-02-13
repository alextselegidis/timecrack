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

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('pages.localization', [
            'defaultLocale' => setting('default_locale', 'en'),
            'defaultTimezone' => setting('default_timezone', 'UTC'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'default_locale' => ['required'],
            'default_timezone' => ['required'],
        ]);

        setting([
            'default_locale' => $request->input('default_locale'),
            'default_timezone' => $request->input('default_timezone'),
        ]);

        return redirect()->route('setup.localization')->with('success', __('record_saved_message'));
    }
}
