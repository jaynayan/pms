<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskCommentPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $project->users()->where('user_id', $user->id)->exists() || $project->created_by === $user->id;
    }

    public function create(User $user, Project $project): bool
    {
        $role = $project->users()->where('user_id', $user->id)->first()?->pivot->role;
        return $project->created_by === $user->id || in_array($role, ['manager', 'member']);
    }

    public function delete(User $user, TaskComment $taskComment): bool
    {
        if ($taskComment->user_id === $user->id) return true;
        
        $project = $taskComment->task->project;
        $role = $project->users()->where('user_id', $user->id)->first()?->pivot->role;
        return $project->created_by === $user->id || $role === 'manager';
    }
}
