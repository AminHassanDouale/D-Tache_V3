<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Project;
use App\Models\Priority;
use App\Models\Status;
use App\Traits\ClearsProperties;
use App\Traits\ResetsPaginationWhenPropsChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, ResetsPaginationWhenPropsChanges, ClearsProperties;

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

    public function filterCount(): int
    {
        return ($this->category_id ? 1 : 0) + ($this->status_id ? 1 : 0) + ($this->priority_id ? 1 : 0) + (strlen($this->name) ? 1 : 0);
    }

    public function projects(): LengthAwarePaginator
    {
        return Project::query()
            ->with(['status', 'category', 'priority'])
            ->withCount(['tasks', 'tasks as incomplete_tasks_count' => function ($query) {
                $query->where('status_id', '!=', 5);
            }])
            ->withAggregate('status', 'name')
            ->withAggregate('category', 'name')
            ->withAggregate('priority', 'name')
            ->when($this->name, fn(Builder $q) => $q->where('name', 'like', "%$this->name%"))
            ->when($this->status_id, fn(Builder $q) => $q->where('status_id', $this->status_id))
            ->when($this->category_id, fn(Builder $q) => $q->where('category_id', $this->category_id))
            ->when($this->priority_id, fn(Builder $q) => $q->where('priority_id', $this->priority_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(100);
            
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'No', 'class' => '', 'sortable' => true],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'status', 'label' => 'Status', 'sortBy' => 'status_name', 'class' => 'hidden lg:table-cell'],
            ['key' => 'No', 'label' => 'No', 'sortBy' => 'No', 'class' => 'hidden lg:table-cell'],

            ['key' => 'category.name', 'label' => 'Category', 'sortBy' => 'category_name', 'class' => 'hidden lg:table-cell'],
            ['key' => 'priority.name', 'label' => 'Priority', 'sortBy' => 'priority_name', 'class' => 'hidden lg:table-cell' ],
        ];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'projects' => $this->projects(),
            'statuses' => Status::all(),
            'priorities' => Priority::all(),
            'categories' => Category::all(),
            'filterCount' => $this->filterCount()
        ];
    }
}; ?>
<div>
    <x-header title="Projects" separator progress-indicator>
        {{-- SEARCH --}}
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Name..." wire:model.live.debounce="name" icon="o-magnifying-glass" clearable />
        </x-slot:middle>

        {{-- ACTIONS --}}
        <x-slot:actions>
            <x-button
                label="Filters"
                icon="o-funnel"
                :badge="$filterCount"
                badge-classes="font-mono"
                @click="$wire.showFilters = true"
                class="bg-base-300"
                responsive />

            <x-button label="Create" icon="o-plus" link="/projects/create" class="btn-primary" responsive />
        </x-slot:actions>
    </x-header>
    <x-card>
        @if($projects->count() > 0)
        <x-table :headers="$headers" :rows="$projects" link="/projects/{id}/edit" :sort-by="$sortBy" with-pagination>
           
            @scope('cell_status', $project)
            <x-badge :value="$project->status->name" :class="$project->status->color" />
            @endscope 
            @scope('cell_tasks', $project)
            <ul>
                @forelse($project->tasks as $task)
                    <li>{{ $task->name }}</li>
                @empty
                    <li>No tasks</li>
                @endforelse
            </ul>
            @endscope
            @scope('cell_No', $project)
            <ul class="px-6 py-4 whitespace-nowrap">
                @if($project->tasks_count > 0)
                    @php
                        $completionPercentage = 100 - ($project->incomplete_tasks_count / $project->tasks_count) * 100;
                        $progressBarColor = $completionPercentage < 100 ? 'bg-yellow-500' : 'bg-green-500';
                        $isCompleted = $completionPercentage >= 100;
                    @endphp
                    <div class="w-full bg-gray-200 rounded-full">
                        <div class="{{ $progressBarColor }} text-xs font-medium text-white text-center p-0.5 leading-none rounded-l-full" style="width: {{ $completionPercentage }}%">
                            {{ number_format($completionPercentage) }}%
                        </div>
                    </div>
                    @if(!$isCompleted)
                        <x-progress class="progress-primary h-0.5" indeterminate />
                    @endif
                @else
                    No tasks
                @endif
            </ul>
            @endscope
        </x-table>
        @else
        <div class="flex items-center justify-center gap-10 mx-auto">
            <div>
                <img src="/images/no-results.png" width="300" />
            </div>
            <div class="text-lg font-medium">
                Désolé {{ Auth::user()->name }}, Pas de Projets.
            </div>
        </div>
        @endif
    </x-card>
</div>
