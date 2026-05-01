<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $user;
    public $actionDescription;

    public function __construct(Task $task, User $user, string $actionDescription)
    {
        $this->task = $task;
        $this->user = $user;
        $this->actionDescription = $actionDescription;
    }
}
