<?php

use App\Models\Task; 
use Illuminate\Support\Facades\DB; 
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public string $period = '-100 days';

    public array $chartCategory = [
        'type' => 'doughnut',
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'left',
                    'labels' => [
                        'usePointStyle' => true
                    ]
                ]
            ],
        ],
        'data' => [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Sold',
                    'data' => [],
                ]
            ]
        ]
    ];

    #[Computed]
    public function refreshChartCategory(): void
    {
        $tasks = Task::query()
            ->selectRaw("count(category_id) as total, category_id")
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->groupBy('category_id')
            ->get();

        Arr::set($this->chartCategory, 'data.labels', $tasks->pluck('category.name'));
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
