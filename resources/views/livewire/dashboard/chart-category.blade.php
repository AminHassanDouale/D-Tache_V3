<?php

use App\Models\Task;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public string $period = '-30 days';

    public array $chartCategory = [
        'type' => 'doughnut',
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'left',
                    'labels' => [
                        'usePointStyle' => true,
                    ],
                ],
            ],
        ],
        'data' => [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => [],
                ],
            ],
        ],
    ];

    #[Computed]
    public function refreshChartCategory(): void
    {
        $departmentId = Auth::user()->department_id;

        $tasks = Task::query()
            ->selectRaw('count(category_id) as total, category_id')
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->whereHas('category', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->groupBy('category_id')
            ->get();

        $categoryNames = Category::whereIn('id', $tasks->pluck('category_id'))->pluck('name');

        Arr::set($this->chartCategory, 'data.labels', $categoryNames);
        Arr::set($this->chartCategory, 'data.datasets.0.data', $tasks->pluck('total'));
    }

    public function with(): array
    {
        $this->refreshChartCategory();

        return [];
    }
}; ?>

<div>
    <x-card title="Categorie" separator shadow>
        <x-chart wire:model="chartCategory" class="h-44" />
    </x-card>
</div>
