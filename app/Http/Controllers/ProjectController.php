<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = $this->projectService->getAccessibleProjects($request->user());
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string',
        ]);

        $project = $this->projectService->createProject($validated, $request->user());

        $project->users()->syncWithoutDetaching([
            $request->user()->id => [
                'role' => config('pms.project_roles.MANAGER', 'manager')
            ]
        ]);
        $project->load(['creator', 'users']);

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with(['creator', 'users', 'tasks'])->findOrFail($id);
        Gate::authorize('view', $project);
        
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string',
        ]);

        $project = $this->projectService->updateProject($project, $validated);

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('delete', $project);
        
        $this->projectService->deleteProject($project);

        return response()->json(null, 204);
    }

    /**
     * Invite a user to the project.
     */
    public function invite(Request $request, string $id)
    {
        $project = Project::findOrFail($id);
        
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'required|string|in:manager,member,viewer',
        ]);

        $user = \App\Models\User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => 'Invited User',
                'role_id' => config('pms.roles.VIEWER', 4),
            ]
        );

        $project->users()->syncWithoutDetaching([
            $user->id => ['role' => $validated['role']]
        ]);

        return response()->json([
            'message' => 'User invited successfully.',
            'user' => $user
        ], 200);
    }
}
