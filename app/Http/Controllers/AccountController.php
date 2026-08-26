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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('pages.account', [
            'user' => $user,
            'tokens' => $user->tokens()->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'timezone' => ['nullable', 'timezone'],
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // The password form of the account page does not submit the timezone.
        if ($request->has('timezone')) {
            $user->timezone = $request->input('timezone') ?: null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('account')->with('success', __('record_saved_message'));
    }

    public function createToken(Request $request)
    {
        $request->validate([
            'token_name' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $token = $user->createToken($request->input('token_name'));

        return redirect()->route('account')->with('new_token', $token->plainTextToken);
    }

    public function revokeToken(Request $request, int $tokenId)
    {
        $user = $request->user();
        $user->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('account')->with('success', __('token_revoked_message'));
    }
}
