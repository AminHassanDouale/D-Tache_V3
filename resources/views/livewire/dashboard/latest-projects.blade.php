<?php

use App\Models\OrderItem;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public string $period = '-30 days';

    public function bestProjects(): Collection
    {
        $departmentId = Auth::user()->department_id;

return Project::withCount('tasks')
    ->where('department_id', $departmentId)
    ->whereHas('tasks', function ($query) {
        $query->where('created_at', '>=', now()->startOfDay()->subDays(100));
    })
    ->get()
    ->sortByDesc('tasks_count')
    ->take(10);
    }

    public function with(): array
    {
        return [
            'bestProjects' => $this->bestProjects(),

    ];
    }
}; ?>

<div>
    <x-card title="Les 10 derniers Projects" separator shadow>
        <x-slot:menu>
            <x-button label="Projects" icon-right="o-arrow-right" link="/projects" class="btn-ghost btn-sm" />
        </x-slot:menu>

        @foreach($bestProjects as $project)
            <x-list-item :item="$project"   sub-value="category.name" avatar="cover"  no-separator>
                <x-slot:actions>
                    <x-badge :value="$project->tasks_count" class="font-bold" />
                </x-slot:actions>
            </x-list-item>
        @endforeach
    </x-card>
</div>

