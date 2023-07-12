<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setting.index');

    }

    /**
     * Update the specified resource in storage.
     */
    public function save(Request $request, Setting $setting)
    {
        //
    }
}
