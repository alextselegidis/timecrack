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

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        $q = $request->query('q');

        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        $sort = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        if ($sort && $direction) {
            $query->orderBy($sort, $direction);
        }

        $projects = $query->paginate(25);

        return view('pages.projects', [
            'projects' => $projects,
            'q' => $q,
        ]);
    }

    public function create()
    {
        $users = User::query()->where('role', '!=', 'admin')->orderBy('name')->get();

        return view('pages.projects-edit', [
            'project' => new Project(),
            'users' => $users,
            'selectedUserIds' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $project = Project::create($request->only(['name', 'description', 'color']));

        $project->users()->sync($request->input('users', []));

        return redirect()->route('setup.projects')->with('success', __('Project created successfully.'));
    }

    public function edit(Project $project)
    {
        $users = User::query()->where('role', '!=', 'admin')->orderBy('name')->get();

        session(['projects_list_url' => url()->previous()]);

        return view('pages.projects-edit', [
            'project' => $project,
            'users' => $users,
            'selectedUserIds' => $project->users->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $project->update($request->only(['name', 'description', 'color']));

        $project->users()->sync($request->input('users', []));

        return redirect()->route('setup.projects')->with('success', __('Project updated successfully.'));
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('setup.projects')->with('success', __('Project deleted successfully.'));
    }
}
