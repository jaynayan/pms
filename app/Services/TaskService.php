<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function getTasksForProject(int $projectId, bool $isBacklog)
    {
        return Task::with(['assignee', 'creator'])
            ->where('project_id', $projectId)
            ->where('is_backlog', $isBacklog)
            ->orderBy('position', 'asc')
            ->get();
    }

    public function createTask(array $data, User $creator)
    {
        $data['creator_id'] = $creator->id;
        if (!isset($data['is_backlog'])) {
            $data['is_backlog'] = true;
        }
        if (!isset($data['status'])) {
            $data['status'] = config('pms.task_statuses.TO_DO', 'To Do');
        }
        
        $maxPosition = Task::where('project_id', $data['project_id'])
            ->where('is_backlog', $data['is_backlog'])
            ->max('position') ?? 0;
            
        $data['position'] = $maxPosition + 1;

        return Task::create($data)->load(['assignee', 'creator']);
    }

    public function claimTask(Task $task, User $user)
    {
        $task->assigned_to = $user->id;
        $task->is_backlog = false;
        $task->status = config('pms.task_statuses.TO_DO', 'To Do');
        
        $maxPosition = Task::where('project_id', $task->project_id)
            ->where('is_backlog', false)
            ->max('position') ?? 0;
            
        $task->position = $maxPosition + 1;
        $task->save();

        return $task->load(['assignee', 'creator']);
    }

    public function updateStatus(Task $task, string $status)
    {
        $task->status = $status;
        
        $maxPosition = Task::where('project_id', $task->project_id)
            ->where('is_backlog', $task->is_backlog)
            ->where('status', $status)
            ->max('position') ?? 0;
            
        $task->position = $maxPosition + 1;
        $task->save();

        return $task->load(['assignee', 'creator']);
    }

    public function updateTask(Task $task, array $data)
    {
        $task->update($data);
        return $task->load(['assignee', 'creator']);
    }

    public function reorderTasks(array $tasks)
    {
        DB::transaction(function () use ($tasks) {
            foreach ($tasks as $taskData) {
                $task = Task::find($taskData['id']);
                if ($task) {
                    $task->position = $taskData['position'];
                    if (isset($taskData['status'])) {
                        $task->status = $taskData['status'];
                    }
                    if (isset($taskData['is_backlog'])) {
                        $task->is_backlog = $taskData['is_backlog'];
                    }
                    $task->save();
                }
            }
        });
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
    }
}
