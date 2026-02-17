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

        $data = $request->only(['name', 'description', 'color']);
        
        // Generate random color if not provided
        if (empty($data['color'])) {
            $data['color'] = $this->generateRandomColor();
        }

        $project = Project::create($data);

        $project->users()->sync($request->input('users', []));

        return redirect()->route('setup.projects.edit', $project->id)->with('success', __('record_saved_message'));
    }

    /**
     * Generate a random pleasant color.
     */
    private function generateRandomColor(): string
    {
        $colors = [
            '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545',
            '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0',
            '#6c757d', '#495057', '#5c636a', '#5a6268', '#4e555b',
            '#2c7be5', '#00d97e', '#e63757', '#f6c343', '#39afd1',
            '#727cf5', '#6b5eae', '#fa5c7c', '#ff6b6b', '#4ecdc4',
            '#45b7d1', '#96ceb4', '#ffeaa7', '#dfe6e9', '#a29bfe',
        ];
        return $colors[array_rand($colors)];
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

        return redirect()->route('setup.projects.edit', $project->id)->with('success', __('record_saved_message'));
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('setup.projects')->with('success', __('record_deleted_message'));
    }
}
