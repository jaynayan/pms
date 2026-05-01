<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskCommentController extends Controller
{
    public function index(string $taskId)
    {
        $task = Task::with('project')->findOrFail($taskId);
        Gate::authorize('viewAny', [TaskComment::class, $task->project]);

        $comments = TaskComment::with('user')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, string $taskId)
    {
        $task = Task::with('project')->findOrFail($taskId);
        Gate::authorize('create', [TaskComment::class, $task->project]);

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'comment' => $validated['comment'],
        ]);
        
        event(new \App\Events\TaskCommentedEvent($comment));

        return response()->json($comment->load('user'));
    }

    public function destroy(string $id)
    {
        $comment = TaskComment::with('task.project')->findOrFail($id);
        Gate::authorize('delete', $comment);

        $comment->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
