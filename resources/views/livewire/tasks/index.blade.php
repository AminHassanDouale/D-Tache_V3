<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Project;
use App\Models\Task;
use App\Models\Priority;
use App\Models\Status;
use App\Traits\ClearsProperties;
use App\Traits\ResetsPaginationWhenPropsChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use toast, WithPagination, ResetsPaginationWhenPropsChanges, ClearsProperties;

    #[Url]
    public string $name = '';

    #[Url]
    public int $status_id = 0;

    #[Url]
    public ?int $category_id = 0;
    #[Url]
    public ?int $priority_id = 0;

    #[Url]
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public bool $showFilters = false;
    public function changeStatus($taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->status_id == 5) {
            $task->update(['status_id' => 2]);
        } else {
            $task->update(['status_id' => 5]);
        }

        $this->toast(type: 'success', title: 'Status Updated!', description: 'Task status has been updated.', position: 'toast-top toast-end', icon: 'o-information-circle', css: 'alert-success', timeout: 3000, redirectTo: null);
    }

    public function filterCount(): int
    {
        return ($this->category_id ? 1 : 0) + ($this->status_id ? 1 : 0) + ($this->priority_id ? 1 : 0) + (strlen($this->name) ? 1 : 0);
    }
    public function statuses(): Collection
    {
        return Status::orderBy('name')->get();
    }
    public function tasks(): LengthAwarePaginator
    {
        return Task::query()
            ->where('assigned_id', Auth::user()->id)
            ->with(['status', 'category', 'priority'])
            ->withAggregate('status', 'name')
            ->withAggregate('category', 'name')
            ->withAggregate('priority', 'name')
            ->when($this->name, fn(Builder $q) => $q->where('name', 'like', "%$this->name%"))
            ->when($this->status_id, fn(Builder $q) => $q->where('status_id', $this->status_id))
            ->when($this->category_id, fn(Builder $q) => $q->where('category_id', $this->category_id))
            ->when($this->priority_id, fn(Builder $q) => $q->where('priority_id', $this->priority_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(30);
    }

    public function headers(): array
    {
        return [ 
        ['key' => 'name', 'label' => 'Name'],

        ['key' => 'status', 'label' => 'Status', 'sortBy' => 'status_name', 'class' => 'hidden lg:table-cell'],

        ['key' => 'category.name', 'label' => 'Category', 'sortBy' => 'category_name', 'class' => 'hidden lg:table-cell'], ['key' => 'priority.name', 'label' => 'Priority', 'sortBy' => 'priority_name'], ['key' => 'user.name', 'label' => 'Assignee', 'sortBy' => 'user_name']];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'tasks' => $this->tasks(),
            'statuses' => Status::all(),
            'priorities' => Priority::all(),
            'categories' => Category::all(),
            'filterCount' => $this->filterCount(),
        ];
    }
    protected $listeners = ['task-saved' => '$refresh'];
}; ?>

<div>
    {{--  HEADER  --}}



    <x-header title="Tasks" separator progress-indicator>
        {{--  SEARCH --}}
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Name..." wire:model.live.debounce="name" icon="o-magnifying-glass" clearable />
        </x-slot:middle>

        {{-- ACTIONS  --}}
        <x-slot:actions>
            <x-button label="Filters" icon="o-funnel" :badge="$filterCount" badge-classes="font-mono"
                @click="$wire.showFilters = true" class="bg-base-300" responsive />

            <livewire:tasks.create />
        </x-slot:actions>
    </x-header>

    {{--  TABLE --}}
    <x-card>
        @if ($tasks->count() > 0)
            <x-table :headers="$headers" :rows="$tasks" link="/tasks/{id}/show" :sort-by="$sortBy" with-pagination>
                @scope('cell_status', $task)
            <x-badge :value="$task->status->name" :class="$task->status->color" />
            @endscope
                @scope('actions', $task)
                    <td class="px-1 py-1 text-sm bg-white border-b border-gray-200">
                        <input type="checkbox" wire:model="selectedTasks.{{ $task->id }}"
                            class="w-4 h-4 bg-blue-900 border-gray-300 rounded focus:ring-blue-900 dark:focus:ring-blue-900 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            wire:change="changeStatus({{ $task->id }})" {{ $task->status_id == 5 ? 'checked' : '' }}>
                    </td>
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

    {{-- FILTERS --}}
    <x-drawer wire:model="showFilters" title="Filters" class="lg:w-1/3" right separator with-close-button>
        <div class="grid gap-5" @keydown.enter="$wire.showFilters = false">
            <x-input label="Name ..." wire:model.live.debounce="name" icon="o-user" inline />
            <x-select label="Status" :options="$statuses" wire:model.live="status_id" icon="o-map-pin" placeholder="All"
                placeholder-value="0" inline />
            <x-select label="Category" :options="$categories" wire:model.live="category_id" icon="o-flag" placeholder="All"
                placeholder-value="0" inline />
            <x-select label="Priority" :options="$priorities" wire:model.live="priority_id" icon="o-flag" placeholder="All"
                placeholder-value="0" inline />
        </div>

        {{-- ACTIONS --}}
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.showFilters = false" />
        </x-slot:actions>
    </x-drawer>
</div>
