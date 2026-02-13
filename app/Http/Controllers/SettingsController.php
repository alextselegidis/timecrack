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
        return view('pages.settings', [
            'timezone' => setting('timezone', 'UTC'),
            'locale' => setting('locale', 'en'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'timezone' => ['required', 'string', 'timezone'],
            'locale' => ['required', 'string', 'in:en,de,fr,es,it,pt,nl,ru,zh,ja'],
        ]);

        setting([
            'timezone' => $request->input('timezone'),
            'locale' => $request->input('locale'),
        ]);

        return redirect()->route('setup.settings')->with('success', __('Settings updated successfully.'));
    }
}
