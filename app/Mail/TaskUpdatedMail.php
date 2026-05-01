<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $actor;
    public $actionDescription;

    public function __construct(Task $task, User $actor, string $actionDescription)
    {
        $this->task = $task;
        $this->actor = $actor;
        $this->actionDescription = $actionDescription;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Update: ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tasks.updated',
        );
    }
}
