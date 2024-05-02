<?php

use App\Models\Task;
use App\Models\Status;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public $statusId; // Add a public property for status_id
    public function mount()
    {
        $this->statusId = null; // Initialize statusId to null
    }
  
  public array $chartTasks = [
    'type' => 'bar',
    'options' => [
      'backgroundColor' => '#dfd7f7',
      'responsive' => true,
      'maintainAspectRatio' => false,
      'scales' => [
        'x' => [
          'display' => true,
          'title' => 'Day-Month',
        ],
        'y' => [
          'display' => true,
          'title' => 'Tasks Count',
        ],
      ],
      'plugins' => [
        'legend' => [
          'display' => false,
        ],
      ],
    ],
    'data' => [
  'labels' => [],
  'datasets' => [],
],

  ];
  

   

  public function refreshChartTasks(): void
  {
    $departmentId = Auth::user()->department_id;

    $tasksQuery = Task::query()
            ->selectRaw("DATE_FORMAT(created_at, '%d-%b-%Y') as day_month, count(*) as total_tasks")
            ->where('department_id', $departmentId)
            ->groupBy('day_month');

        if ($this->statusId) {
            $tasksQuery->where('status_id', $this->statusId);
        }

        $tasks = $tasksQuery->get();

        // Update chartTasks with new data
        Arr::set($this->chartTasks, 'data.labels', $tasks->pluck('day_month'));
        Arr::set($this->chartTasks, 'data.datasets.0.data', $tasks->pluck('total_tasks'));

  }

  public function with(): array
  {
    $this->refreshChartTasks();

    return [
      'chartTasks' => $this->chartTasks,
      'statuses' =>   Status::All(),
    ];
  }
};
 ?>
<div>
    <x-card title="Tasks" separator shadow>
        <x-chart wire:model="chartTasks" class="h-44" />
    </x-card>
</div>

