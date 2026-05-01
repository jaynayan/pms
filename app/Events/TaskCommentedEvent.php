<?php

namespace App\Events;

use App\Models\TaskComment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCommentedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    public function __construct(TaskComment $comment)
    {
        $this->comment = $comment;
    }
}
