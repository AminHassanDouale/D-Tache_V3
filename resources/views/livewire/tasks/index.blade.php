<?php

use App\Models\Task;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Mary\Traits\Toast;


new class extends Component {
    use WithPagination;
    use Toast;


    #[Url]
    public string $search = '';

    #[Url]
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public function tasks(): LengthAwarePaginator
    {
        return Task::query()
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(9);
    }
    public function updateSelectedStatus($taskId)
{
   // Extract the IDs of the displayed tasks
   $taskIds = $this->tasks->pluck('id');

// Update the status_id of all displayed tasks to 3
Task::whereIn('id', $taskIds)->update(['status_id' => 3]);

// Re-fetch the tasks with updated status
$this->tasks = $this->tasks();

// Show success toast
$this->toast('Status updated successfully.', 'success');
}


    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-20'],
            ['key' => 'name', 'label' => 'Name'],
        ];
    }

    public function with(): array
    {
        return [
            'tasks' => $this->tasks(),
            'headers' => $this->headers()
        ];
    }
    protected $listeners = ['task-saved' => '$refresh'];

};
?>
<div>
    {{-- HEADER --}}
    <x-header title="Tasks" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" clearable />
        </x-slot:middle>
         <x-slot:actions>
            <livewire:tasks.create />
        </x-slot:actions>
       
    </x-header>

    {{-- TABLE --}}
    <x-card>
        @if($tasks->count() > 0)
            <x-table :headers="$headers" :rows="$tasks" :sort-by="$sortBy" with-pagination>
                {{-- Table content here --}}
                @scope('actions', $task)
                <x-button :link="'/tasks/' . $task->id . '/edit'" icon="o-eye" class="btn-sm btn-ghost text-error" spinner />

                @endscope
            </x-table>

        @else
            <div class="flex items-center justify-center gap-10 mx-auto">
                <div>
                    <img src="/images/no-results.png" width="300" />
                </div>
                <div class="text-lg font-medium">
                    Desole {{ Auth::user()->name }}, Pas des Tasks.
                </div>
            </div>
        @endif
    </x-card>
    

    {{--   EDIT MODAL --}}
</div>
