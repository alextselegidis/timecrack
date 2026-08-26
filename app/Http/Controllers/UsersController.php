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

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $q = $request->query('q');

        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            });
        }

        $sort = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        if ($sort && $direction) {
            $query->orderBy($sort, $direction);
        }

        $users = $query->paginate(25);

        return view('pages.users', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('pages.users-edit', [
            'user' => new User(),
            'roles' => RoleEnum::values(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => 'new-user-' . strtolower(Str::random(5)) . '@example.org',
            'password' => Hash::make(Str::random(8)),
        ]);

        return redirect()->route('setup.users.edit', ['user' => $user->id]);
    }

    public function edit(User $user)
    {
        return view('pages.users-edit', [
            'user' => $user,
            'roles' => RoleEnum::values(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'timezone' => ['nullable', 'timezone'],
            'role' => ['required', 'string', Rule::in(RoleEnum::values())],
            'is_active' => ['boolean'],
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->timezone = $request->input('timezone') ?: null;
        $user->role = $request->input('role');
        $user->is_active = $request->boolean('is_active', true);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('setup.users.edit', $user->id)->with('success', __('record_saved_message'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('setup.users')->with('success', __('record_deleted_message'));
    }
}
