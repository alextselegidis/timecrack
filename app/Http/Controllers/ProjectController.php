<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller {
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
        return view('project.create');
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
        return view('project.edit', [
            'project' => $project,
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

        $project->save();

        return back();
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
