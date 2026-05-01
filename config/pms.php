<?php

return [
    'roles' => [
        'ADMIN' => 1,
        'PROJECT_MANAGER' => 2,
        'TEAM_MEMBER' => 3,
        'VIEWER' => 4,
    ],
    'project_roles' => [
        'MANAGER' => 'manager',
        'MEMBER' => 'member',
        'VIEWER' => 'viewer',
    ],
    'project_statuses' => [
        'PLANNING' => 'Planning',
        'IN_PROGRESS' => 'In Progress',
        'REVIEW' => 'Review',
        'COMPLETED' => 'Completed',
    ],
    'task_statuses' => [
        'TO_DO' => 'To Do',
        'IN_PROGRESS' => 'In Progress',
        'DONE' => 'Done',
    ]
];
