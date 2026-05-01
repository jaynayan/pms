<?php

namespace App\Listeners;

use App\Events\TaskUpdatedEvent;
use App\Events\TaskCommentedEvent;
use App\Mail\TaskUpdatedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Events\Dispatcher;

class SendTaskUpdateNotification implements ShouldQueue
{
    public function handleTaskUpdated(TaskUpdatedEvent $event): void
    {
        $this->sendNotifications($event->task, $event->user, $event->actionDescription);
    }

    public function handleTaskCommented(TaskCommentedEvent $event): void
    {
        $task = $event->comment->task;
        $user = $event->comment->user;
        $this->sendNotifications($task, $user, 'Added a new comment: "' . \Illuminate\Support\Str::limit($event->comment->comment, 50) . '"');
    }

    protected function sendNotifications($task, $actor, $actionDescription)
    {
        $recipientIds = collect();
        if ($task->creator_id) {
            $recipientIds->push($task->creator_id);
        }
        if ($task->assigned_to) {
            $recipientIds->push($task->assigned_to);
        }
        
        $commenterIds = \App\Models\TaskComment::where('task_id', $task->id)->pluck('user_id');
        $recipientIds = $recipientIds->concat($commenterIds);

        // Exclude the actor
        $recipientIds = $recipientIds->reject(function ($id) use ($actor) {
            return $id == $actor->id;
        })->unique();

        $recipients = User::whereIn('id', $recipientIds)->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(new TaskUpdatedMail($task, $actor, $actionDescription));
        }
    }
    
    public function subscribe(Dispatcher $events): array
    {
        return [
            TaskUpdatedEvent::class => 'handleTaskUpdated',
            TaskCommentedEvent::class => 'handleTaskCommented',
        ];
    }
}
