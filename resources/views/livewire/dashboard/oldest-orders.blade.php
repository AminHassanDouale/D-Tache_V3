<?php

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public string $period = '-100 days';

    public bool $show = false;
    public ?Task $task = null;

    // Triggers the order preview drawer
   

    public function orders(): Collection
    {
        return Task::with(['user', 'status'])
            ->where('created_at', '>=', Carbon::parse($this->period)->startOfDay())
            ->oldest('id')
            ->take(5)
            ->get();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'py-4', 'class' => 'hidden lg:table-cell'],
            ['key' => 'name', 'label' => 'Task'],
            ['key' => 'user.name', 'label' => 'User'],
            ['key' => 'status.name', 'label' => 'Status', 'class' => 'hidden lg:table-cell']
        ];

    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'tasks' => $this->orders() // Use orders() method to fetch tasks
        ];
    }
};
 ?>


<div>
    <x-card title="Tasks a faire" separator shadow progress-indicator class="mt-10">
        <x-slot:menu>
            <x-button label="tasks" icon-right="o-arrow-right" link="/tasks" class="btn-ghost btn-sm" />
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$tasks" @row-click="$wire.preview($event.detail.id)">
          
        </x-table>

        @if(!$tasks->count())
            <x-icon name="o-list-bullet" label="Nothing here." class="mt-5 text-gray-400" />
        @endif
    </x-card>

  
</div>
