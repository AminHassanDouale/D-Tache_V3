<?php

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public string $period = '-30 days';

    // General statistics
    public function stats(): array
    {
        $newCustomers = User::query()
            ->where('created_at', '>=', Carbon::parse($this->period)->startOfDay())
            ->count();

        $tasks = Task::query()
        ->where('user_id', Auth::user()->id)
        ->where('assigned_id', Auth::user()->id)
            ->where('created_at', '>=', Carbon::parse($this->period)->startOfDay())
            ->count();

        $taskpending= Task::query()
        ->where('user_id', Auth::user()->id)
        ->where('assigned_id', Auth::user()->id)
            ->where('created_at', '>=', Carbon::parse($this->period)->startOfDay())
            ->where('status_id', '!=', 5)
            ->count();
        $taskterminated= Task::query()
        ->where('user_id', Auth::user()->id)
        ->where('assigned_id', Auth::user()->id)
            ->where('created_at', '>=', Carbon::parse($this->period)->startOfDay())
            ->where('status_id', '=', 5)
            ->count();
            $taskDelayed = Task::query()
            ->where('user_id', Auth::user()->id)
            ->where('assigned_id', Auth::user()->id)
            ->where('created_at', '>=', Carbon::parse($this->period)->startOfDay())
            ->where('status_id', '!=', 5)
            ->whereDate('due_date', '<=', Carbon::now()) 
            ->count(); 

        return [
            'newCustomers' => $newCustomers,
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
