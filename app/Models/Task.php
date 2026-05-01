<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\TaskObserver;

#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'creator_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'is_backlog',
        'position',
        'original_estimate',
        'total_spent',
    ];

    protected $casts = [
        'is_backlog' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
