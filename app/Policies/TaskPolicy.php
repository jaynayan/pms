<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;

class TaskPolicy
{
    private function getPivotRole(User $user, int $projectId): ?string
    {
        $project = Project::find($projectId);
        if (!$project) return null;
        
        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        return $pivot ? $pivot->role : null;
    }

    private function canAccessProject(User $user, int $projectId): bool
    {
        if ($user->isAdmin()) return true;
        
        $project = Project::find($projectId);
        if (!$project) return false;

        return $project->users()->where('user_id', $user->id)->exists();
    }

    public function viewAny(User $user): bool
    {
        return true; // Filtered by controller/service
    }

    public function view(User $user, Task $task): bool
    {
        return $this->canAccessProject($user, $task->project_id);
    }

    public function create(User $user, Project $project = null): bool
    {
        if ($user->isAdmin()) return true;
        
        if ($project) {
            $role = $this->getPivotRole($user, $project->id);
            return in_array($role, [config('pms.project_roles.MANAGER', 'manager'), config('pms.project_roles.MEMBER', 'member')]);
        }
        return false;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isAdmin()) return true;

        $role = $this->getPivotRole($user, $task->project_id);
        return in_array($role, [config('pms.project_roles.MANAGER', 'manager'), config('pms.project_roles.MEMBER', 'member')]);
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->isAdmin()) return true;

        $role = $this->getPivotRole($user, $task->project_id);
        if ($role === config('pms.project_roles.MANAGER', 'manager')) return true;
        
        // Task creator can delete their own tasks
        return $task->creator_id === $user->id;
    }

    public function claim(User $user, Task $task): bool
    {
        if ($user->isAdmin()) return true;

        $role = $this->getPivotRole($user, $task->project_id);
        return in_array($role, [config('pms.project_roles.MANAGER', 'manager'), config('pms.project_roles.MEMBER', 'member')]);
    }

    public function assign(User $user, Project $project, $assigneeId): bool
    {
        // 1. Viewers are blocked implicitly via getPivotRole check below
        $role = $this->getPivotRole($user, $project->id);
        
        if (!$user->isAdmin() && !in_array($role, [config('pms.project_roles.MANAGER', 'manager'), config('pms.project_roles.MEMBER', 'member')])) {
            return false;
        }

        // 2. Validate Assignee exists in project_user (if not unassigning)
        if ($assigneeId !== null && !$project->users()->where('user_id', $assigneeId)->exists()) {
            return false;
        }

        // 3. Member -> assign ONLY to themselves (cannot assign to others, but can unassign themselves or assign themselves)
        if (!$user->isAdmin() && $role === config('pms.project_roles.MEMBER', 'member')) {
            // They can assign themselves, or unassign themselves (we'll allow them to pass null strictly if they want to unassign)
            return $assigneeId === $user->id || $assigneeId === null; 
        }

        // 4. Manager/Admin -> assign to anyone in project
        return true;
    }

    public function updateStatus(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
