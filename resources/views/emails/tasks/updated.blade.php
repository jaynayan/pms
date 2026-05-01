<x-mail::message>
# Task Updated: {{ $task->title }}

Hello,

**{{ $actor->name }}** has performed the following action on this task:

**{{ $actionDescription }}**

<x-mail::button :url="url('/?project_id=' . $task->project_id)">
View Task
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
