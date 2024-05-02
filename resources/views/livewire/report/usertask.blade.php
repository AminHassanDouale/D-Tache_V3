<?php

use App\Models\Task;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    public $users;

    public function mount()
    {
        $this->users = User::withCount(['tasks' => function ($query) {
            $query->where('status_id', 5); // Filter tasks by status_id
        }])->where('department_id', Auth::user()->department_id)->get();
    }

    public function with(): array
    {
        
        return [
            'users' => $this->users,

    ];
    }
}; ?>

<div>
    <div>
        <x-card title="Task Realise" separator shadow>
            @if ($users->isEmpty())
            <p>No users with tasks were found.</p>
        @else
            @foreach ($users as $user)
                <p>{{ $user->name }}: {{ $user->tasks_count }} / {{ $user->tasks->count() }}</p>
            @endforeach
        @endif
        </x-card>
    </div>
</div>
