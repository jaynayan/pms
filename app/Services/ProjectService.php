<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;

class ProjectService
{
    public function getAccessibleProjects(User $user)
    {
        return Project::with(['creator', 'users'])
            ->accessibleBy($user)
            ->latest()
            ->get();
    }

    public function createProject(array $data, User $creator)
    {
        $project = new Project($data);
        $project->created_by = $creator->id;
        $project->save();

        return $project->load(['creator', 'users']);
    }

    public function updateProject(Project $project, array $data)
    {
        $project->update($data);

        return $project->load(['creator', 'users']);
    }

    public function deleteProject(Project $project)
    {
        $project->delete();
    }
}
