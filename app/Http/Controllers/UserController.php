<?php

namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('user.index');
    }

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index(Request $request)
    // {
    //     $query = User::query()->orderBy('updated_at', 'desc');
    //
    //     $q = $request->query('q');
    //
    //     if ($q)
    //     {
    //         $query->where('first_name', 'like', '%' . $q . '%');
    //     }
    //
    //     $users = $query->cursorPaginate(25);
    //
    //     return view('user.index', [
    //         'users' => $users,
    //         'q' => $q
    //     ]);
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|min:2',
            'first_name' => 'required|min:2',
            'last_name' => 'nullable|min:2',
            'email' => 'required|min:2',
            'phone' => 'nullable|min:2',
            'phone_alt' => 'nullable|min:2',
            'gender' => 'nullable|in:male,female,other',
            'street' => 'nullable|min:2',
            'street_additional' => 'nullable|min:2',
            'city' => 'nullable|min:2',
            'state' => 'nullable|min:2',
            'postcode' => 'nullable|min:2',
            'country' => 'nullable|min:2',
            'birthdate' => 'nullable|min:2',
            'role' => 'in:user,admin',
            'password' => 'required|min:8|confirmed',
        ]);

        $payload = $request->input();

        $user = User::create($payload);

        return redirect(route('user.show', ['user' => $user->id]))->with('success', __('User created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('user.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('user.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'title' => 'nullable|min:2',
            'first_name' => 'required|min:2',
            'last_name' => 'nullable|min:2',
            'email' => 'required|min:2',
            'phone' => 'nullable|min:2',
            'phone_alt' => 'nullable|min:2',
            'gender' => 'nullable|in:male,female,other',
            'street' => 'nullable|min:2',
            'street_additional' => 'nullable|min:2',
            'city' => 'nullable|min:2',
            'state' => 'nullable|min:2',
            'postcode' => 'nullable|min:2',
            'country' => 'nullable|min:2',
            'birthdate' => 'nullable|min:2',
            'role' => 'in:user,admin',
            'password' => 'nullable|min:8|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'nullable|min:8'
        ]);

        $payload = $request->input();

        if (empty($payload['password']))
        {
            unset($payload['password'], $payload['password_confirmation']);
        }

        $user->fill($payload);

        $user->save();

        return redirect(route('user.show', $user->id))->with('success', __('User updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::user()->id)
        {
            return redirect()->route('user.index')
                ->with('error', __('Cannot delete current user.'));
        }

        $user->delete();

        return redirect()->route('user.index')
            ->with('success', __('User deleted successfully.'));
    }
}
