<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskActivityLog;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    public function updated(Task $task): void
    {
        $userId = Auth::id(); // Could be null if updated via console
        $user = $userId ? \App\Models\User::find($userId) : null;

        if ($task->isDirty('status')) {
            TaskActivityLog::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'action' => 'Changed Status',
                'old_value' => $task->getOriginal('status'),
                'new_value' => $task->status,
            ]);
            if ($user) event(new \App\Events\TaskUpdatedEvent($task, $user, "Changed status from {$task->getOriginal('status')} to {$task->status}"));
        }

        if ($task->isDirty('assigned_to')) {
            $oldAssignee = $task->getOriginal('assigned_to') ? \App\Models\User::find($task->getOriginal('assigned_to'))?->name : 'Unassigned';
            $newAssignee = $task->assigned_to ? \App\Models\User::find($task->assigned_to)?->name : 'Unassigned';
            
            TaskActivityLog::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'action' => 'Changed Assignee',
                'old_value' => $oldAssignee,
                'new_value' => $newAssignee,
            ]);
            if ($user) event(new \App\Events\TaskUpdatedEvent($task, $user, "Changed assignee from {$oldAssignee} to {$newAssignee}"));
        }

        if ($task->isDirty('original_estimate')) {
            TaskActivityLog::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'action' => 'Updated Estimate',
                'old_value' => $task->getOriginal('original_estimate'),
                'new_value' => $task->original_estimate,
            ]);
            if ($user) event(new \App\Events\TaskUpdatedEvent($task, $user, "Updated estimate from {$task->getOriginal('original_estimate')} to {$task->original_estimate} hours"));
        }
    }
}
