<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->isAdmin()) return true;

        return $project->created_by === $user->id || $project->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isPM();
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->isAdmin()) return true;

        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        if ($pivot && $pivot->role === config('pms.project_roles.MANAGER', 'manager')) return true;

        return $user->isPM() && $project->created_by === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->isAdmin()) return true;

        $pivot = $project->users()->where('user_id', $user->id)->first()?->pivot;
        if ($pivot && $pivot->role === config('pms.project_roles.MANAGER', 'manager')) return true;

        return $user->isPM() && $project->created_by === $user->id;
    }
}
