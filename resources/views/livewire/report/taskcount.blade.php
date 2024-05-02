<?php

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {

    public function stats(): array
    {
        $departmentId = Auth::user()->department_id;


        $tasks = Task::query()->where('department_id', $departmentId)->count();

        $taskpending= Task::query()
            ->where('department_id', $departmentId)
            ->where('status_id', '!=', 5)
            ->count();
        $taskterminated= Task::query()
            ->where('department_id', $departmentId)
            ->where('status_id', '=', 5)
            ->count();
            $taskDelayed = Task::query()
            ->where('department_id', $departmentId)
            ->where('status_id', '!=', 5)
            ->whereDate('due_date', '<=', Carbon::now()) 
            ->count(); 

        return [
            'tasks' => $tasks,
            'taskpending' => $taskpending,
            'taskterminated' => $taskterminated,
            'taskDelayed' => $taskDelayed,
        ];
    }

    public function with(): array
    {
        return [
            'stats' => $this->stats(),
        ];
    }
}; ?>

<div>
    <div class="grid gap-5 lg:grid-cols-4 lg:gap-8">
        <x-stat :value="$stats['tasks']" title="Tasks" icon="o-clipboard-document-list" class="shadow" />
        <x-stat :value="$stats['taskterminated']" title="Terminer" icon="o-document-check" class="truncate shadow text-ellipsis" />
        <x-stat :value="$stats['taskpending']" title="En Progress" icon="o-document-duplicate" class="truncate shadow text-ellipsis" />
        <x-stat :value="$stats['taskDelayed']" title="Retard" icon="o-minus-circle" class="truncate shadow text-ellipsis" />
    </div>
</div>
