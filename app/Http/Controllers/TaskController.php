<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource (Board Tasks).
     */
    public function index(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id'
        ]);

        $project = \App\Models\Project::findOrFail($request->project_id);
        Gate::authorize('view', $project);

        $tasks = $this->taskService->getTasksForProject($request->project_id, false);
        return response()->json($tasks);
    }

    /**
     * Display a listing of backlog tasks.
     */
    public function backlog(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id'
        ]);

        $project = \App\Models\Project::findOrFail($request->project_id);
        Gate::authorize('view', $project);

        $tasks = $this->taskService->getTasksForProject($request->project_id, true);
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:Low,Medium,High',
            'is_backlog' => 'nullable|boolean',
            'status' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'original_estimate' => 'nullable|numeric|min:0',
        ]);

        $project = \App\Models\Project::findOrFail($validated['project_id']);
        Gate::authorize('create', [Task::class, $project]);

        if (array_key_exists('assigned_to', $validated)) {
            Gate::authorize('assign', [Task::class, $project, $validated['assigned_to']]);
        }

        $task = $this->taskService->createTask($validated, $request->user());
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with(['assignee', 'creator'])->findOrFail($id);
        Gate::authorize('view', $task);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('update', $task);
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:Low,Medium,High',
            'assigned_to' => 'nullable|exists:users,id',
            'original_estimate' => 'nullable|numeric|min:0',
        ]);

        if (array_key_exists('assigned_to', $validated) && $validated['assigned_to'] != $task->assigned_to) {
            $project = \App\Models\Project::findOrFail($task->project_id);
            Gate::authorize('assign', [Task::class, $project, $validated['assigned_to']]);
        }

        $task = $this->taskService->updateTask($task, $validated);
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('delete', $task);
        $this->taskService->deleteTask($task);
        return response()->json(null, 204);
    }

    public function history(string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('view', $task);

        $logs = \App\Models\TaskActivityLog::with('user')
            ->where('task_id', $id)
            ->latest()
            ->get();

        return response()->json($logs);
    }

    /**
     * Claim the specified task.
     */
    public function claim(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('claim', $task);
        $task = $this->taskService->claimTask($task, $request->user());
        return response()->json($task);
    }

    /**
     * Update the status of the specified task.
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $task = Task::findOrFail($id);
        Gate::authorize('updateStatus', $task);
        $task = $this->taskService->updateStatus($task, $request->status);
        return response()->json($task);
    }

    /**
     * Reorder tasks.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.position' => 'required|integer',
            'tasks.*.status' => 'nullable|string',
            'tasks.*.is_backlog' => 'nullable|boolean',
        ]);

        if (empty($request->tasks)) return response()->json([]);

        $taskIds = array_column($request->tasks, 'id');
        $tasks = Task::whereIn('id', $taskIds)->get();
        $projectIds = $tasks->pluck('project_id')->unique();

        if ($projectIds->count() > 1) {
            abort(403, 'Tasks must belong to the same project.');
        }

        Gate::authorize('update', $tasks->first());

        $this->taskService->reorderTasks($request->tasks);
        return response()->json(['message' => 'Tasks reordered successfully']);
    }
}
