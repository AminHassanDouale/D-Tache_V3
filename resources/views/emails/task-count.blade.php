<x-mail::message>
# Introduction

Salam, c'est la tâche à accomplir.
| Task Name | Status | Due Date   |
|-----------|-------------|------------|
@foreach($tasks as $task)
| {{ $task->name }} | {{ $task->status->name }} | {{ $task->due_date->format('d-m-Y') }} |
@endforeach

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
