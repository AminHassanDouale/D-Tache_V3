<?php

use App\Models\Task;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public string $period = '-100 days';

    public array $chartTasks = [
        'type' => 'line',
        'options' => [
            'backgroundColor' => '#dfd7f7',
            'resposive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'display' => false
                ],
                'y' => [
                    'display' => false
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ]
            ],
        ],
        'data' => [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Task',
                    'data' => [],
                    'tension' => '0.1',
                    'fill' => true,
                ],
            ]
        ]
    ];

    #[Computed]
    public function refreshChartTasks(): void
    {
        $tasks = Task::query()
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as day, count(*) as total_tasks")
        ->groupBy('day')
            ->where('created_at', '>=', Carbon::parse($this->period)->startOfDay())
            ->get();

        Arr::set($this->chartTasks, 'data.labels', $tasks->pluck('day'));
        Arr::set($this->chartTasks, 'data.datasets.0.data', $tasks->pluck('total_tasks'));
    }

    public function with(): array
    {
        $this->refreshChartTasks();

        return [
            'chartTasks' => $this->chartTasks,
        ];
    }
}; ?>

<div>
    <x-card title="Tasks" separator shadow>
        <x-chart wire:model="chartTasks" class="h-44" />
    </x-card>
</div>
