<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller {
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::query()->orderBy('updated_at', 'desc');

        $q = $request->query('q');

        if ($q)
        {
            $query->where('name', 'like', '%' . $q . '%');
        }

        if ($request->user()->role === RoleEnum::USER->value) {
            $query
                ->join('project_user', 'project_user.project_id', '=', 'projects.id')
                ->where('project_user.user_id', $request->user()->id);
        }

        $projects = $query->cursorPaginate(25);

        return view('project.index', [
            'projects' => $projects,
            'q' => $q
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userOptions = User::toOptions([['role', '!=', RoleEnum::ADMIN->value]]);

        return view('project.create', [
            'userOptions' => $userOptions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2',
            'description' => 'nullable|min:2',
        ]);

        $payload = $request->input();

        $project = Project::create($payload);

        $project->users()->sync($payload['users']);

        return redirect(route('project.show', $project->id))->with('success', __('Project created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return view('project.show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $userOptions = User::toOptions([['role', '!=', RoleEnum::ADMIN->value]]);

        return view('project.edit', [
            'project' => $project,
            'userOptions' => $userOptions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|min:2',
            'description' => 'nullable|min:2',
        ]);

        $payload = $request->input();

        $project->fill($payload);

        $project->users()->sync($payload['users']);

        $project->save();

        return redirect(route('project.show', $project->id))->with('success', __('Project updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('project.index')
            ->with('success', __('Project deleted successfully.'));
    }
}
