<?php

use App\Models\Student;
use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url]
    public string $name = '';

    #[Url]
    public int $status_id = 0;

    #[Url]
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public bool $showFilters = false;

    public function filterCount(): int
    {
        return ($this->status_id ? 1 : 0) + (strlen($this->name) ? 1 : 0);
    }

    public function students(): LengthAwarePaginator
    {
        return Student::query()
            ->with(['status'])
            ->when($this->name, fn(Builder $q) => $q->where('name', 'like', "%$this->name%"))
            ->when($this->status_id, fn(Builder $q) => $q->where('status_id', $this->status_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(100);
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'No', 'class' => '', 'sortable' => true],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'status', 'label' => 'Status', 'sortBy' => 'status_name', 'class' => 'hidden lg:table-cell'],
            ['key' => 'action', 'label' => 'action', 'sortBy' => '', 'class' => 'hidden lg:table-cell'],
        ];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'students' => $this->students(),
            'statuses' => Status::all(),
            'filterCount' => $this->filterCount()
        ];
    }
};
?>
<div>
    <x-header title="Students" separator progress-indicator>
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

            <x-button label="Create" icon="o-plus" link="/students/create" class="btn-primary" responsive />
        </x-slot:actions>
    </x-header>
    <x-card>
        @if($students->count() > 0)
        <x-table :headers="$headers" :rows="$students" link="/students/{id}/show" :sort-by="$sortBy" with-pagination>
            @scope('cell_status', $student)
            <x-badge :value="$student->status->name" :class="$student->status->color" />
            @endscope
            @scope('cell_action', $student)

                <x-button label="Edit" link="/students/{{ $student->id }}/edit"  icon="o-pencil" class="btn-primary" responsive />
                    @endscope
                </x-table>
        @else
        <div class="flex items-center justify-center gap-10 mx-auto">
            <div>
                <img src="/images/no-results.png" width="300" />
            </div>
            <div class="text-lg font-medium">
                Sorry {{ Auth::user()->name }}, No Students.
            </div>
        </div>
        @endif
    </x-card>

    <x-drawer wire:model="showFilters" title="Filters" class="lg:w-1/3" right separator with-close-button>
        <div class="grid gap-5" @keydown.enter="$wire.showFilters = false">
            <x-input label="Name ..." wire:model.live.debounce="name" icon="o-user" inline />
            <x-select label="Status" :options="$statuses" wire:model.live="status_id" icon="o-map-pin" placeholder="All"
                placeholder-value="0" inline />
          
        </div>

        {{-- ACTIONS --}}
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.showFilters = false" />
        </x-slot:actions>
    </x-drawer>
</div>
