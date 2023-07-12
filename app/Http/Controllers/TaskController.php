<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query()->orderBy('updated_at', 'desc');

        $q = $request->query('q');

        if ($q)
        {
            $query->where('summary', 'like', '%' . $q . '%');
        }

        $query->with(['project', 'user']);

        $tasks = $query->cursorPaginate(25);

        return view('task.index', [
            'tasks' => $tasks,
            'q' => $q
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userOptions = User::toOptions();
        $projectOptions = Project::toOptions();

        return view('task.create', [
            'userOptions' => $userOptions,
            'projectOptions' => $projectOptions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:App\Models\Project,id',
            'user_id' => 'required|exists:App\Models\User,id',
            'started_at' => 'required|date|before:ended_at',
            'ended_at' => 'required|date|after:started_at',
            'summary' => 'required|min:2',
        ]);

        $payload = $request->input();

        $task = Task::create($payload);

        return redirect(route('task.show', $task->id))->with('success', __('Task created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['project', 'user']);

        return view('task.show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $userOptions = User::toOptions();
        $projectOptions = Project::toOptions();

        return view('task.edit', [
            'task' => $task,
            'userOptions' => $userOptions,
            'projectOptions' => $projectOptions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'project_id' => 'required|exists:App\Models\Project,id',
            'user_id' => 'required|exists:App\Models\User,id',
            'started_at' => 'required|date|before:ended_at',
            'ended_at' => 'required|date|after:started_at',
            'summary' => 'required|min:2',
        ]);

        $payload = $request->input();

        $task->fill($payload);

        $task->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('task.index')
            ->with('success', __('Task deleted successfully.'));
    }
}
