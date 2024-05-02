<?php

use App\Models\Task;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public $startDate;
    public $dueDate;
    public string $taskName = '';
    public ?int $category_id = 0;
    public ?int $userId = 0; // Assuming user_id is the foreign key in the tasks table




    public $tasks;

public function mount()
{
    $this->tasks = Task::query()->get();
    
    
}

    public function reportdept()
    {
        $tasksQuery = Task::query();

// Check if both start date and due date are provided
if ($this->startDate && $this->dueDate) {
    $tasksQuery->where(function ($query) {
        $query->whereBetween('start_date', [$this->startDate, $this->dueDate])
              ->orWhereBetween('due_date', [$this->startDate, $this->dueDate]);
    });
} 
// Check if only start date is provided
elseif ($this->startDate) {
    $tasksQuery->where('start_date', '>=', $this->startDate);
} 
// Check if only due date is provided
elseif ($this->dueDate) {
    $tasksQuery->where('due_date', '<=', $this->dueDate);
}

// Apply task name search condition if provided
if ($this->taskName) {
    $tasksQuery->where('name', 'like', '%' . $this->taskName . '%');
}
         // Apply category filter if category ID is provided
         if ($this->category_id) {
            $tasksQuery->where('category_id', $this->category_id);
        }
          // Apply user filter if user ID is provided
          if ($this->userId) {
            $tasksQuery->where('user_id', $this->userId);
        }



// Retrieve paginated tasks
$this->tasks = $tasksQuery->get(); // Change 10 to the desired number of tasks per page

}

    public function with(): array
    {
        $departmentId = Auth::user()->department_id;

        // Query categories where department_id matches the authenticated user's department ID
        $categories = Category::where('department_id', $departmentId)->get();
        $users = User::where('department_id', $departmentId)->get();
        return [
           
            'tasksCount' => $this->tasks->count(),
            'categories' => $categories,
            'users' => $users,
        ];
    }

}; 
 ?>

<div>

    <livewire:report.taskcount />
    <br>
    <div class="grid gap-8 mt-8 lg:grid-cols-6">
        <div class="col-span-6 lg:col-span-4">

    <livewire:report.chart  />
        </div>
        <div class="col-span-6 lg:col-span-2">

    <livewire:report.usertask />
        </div>
    </div>
    

    <div class="grid gap-8 mt-8 lg:grid-cols-5">
        <div class="col-span-6 lg:col-span-4">
    @php
    $config1 = ['altFormat' => 'd/m/Y'];
    $config2 = ['mode' => 'range'];
@endphp
<x-card>
    <x-form wire:submit.prevent="reportdept">
        <div class="mb-4" >
        <x-input label="name" type="text" name="task_name" wire:model="taskName" value="{{ $taskName ?? '' }}" />
    
        <x-datepicker label="start date" name="start_date" wire:model="startDate" value="{{ $startDate ?? '' }}" :config="$config1" />

        <x-datepicker label="due date" name="due_date" wire:model="dueDate" value="{{ $dueDate ?? '' }}" :config="$config1" />
        </div>

        <x-select label="Categories" :options="$categories" wire:model.live="category_id" icon="o-map-pin" placeholder="All" placeholder-value="0" inline />
        <x-select label="Users" :options="$users" wire:model.live="userId" icon="o-user" placeholder="All" placeholder-value="0" inline />





        <button type="submit">Search</button>
    </x-form>
</x-card>
        
    
<x-card title="Results" separator shadow class="mt-2" >
    @if ($startDate || $dueDate || $taskName || $category_id || $userId)

    @if ($tasks->isEmpty())
    <p>No tasks found 😔.</p>
@else
    <x-button label="{{ $tasksCount }} 😃" class="btn-success btn-sm" />

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Date</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->start_date->format('d/m/y') }}</td>
                    <td>{{ $task->due_date->format('d/m/y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @endif
    @endif

</x-card>
</div>
    </div>
</div>