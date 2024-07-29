<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\Member;
use Carbon\Carbon;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $greeting = '';
    public $projects = [];
    public $assignedTasks = [];


    public function mount()
    {
        $this->greeting = $this->getGreeting();
        $this->projects = $this->getProjects();
        $this->assignedTasks = $this->getAssignedTasks();


    }

    // General statistics
    public function tasks(): array
    {
        $userId = Auth::user()->id;

        $taskCount = Task::where('assigned_id', $userId)->count();

        $taskPending = Task::where('assigned_id', $userId)
            ->where('status_id', '!=', 5)
            ->count();

        $taskTerminated = Task::where('assigned_id', $userId)
            ->where('status_id', '=', 5)
            ->count();

        $taskDelayed = Task::where('assigned_id', $userId)
            ->where('status_id', '!=', 5)
            ->whereDate('due_date', '<=', Carbon::now())
            ->count();

        return [
            'taskCount' => $taskCount,
            'taskPending' => $taskPending,
            'taskTerminated' => $taskTerminated,
            'taskDelayed' => $taskDelayed,
        ];
    }

    public function getProjects()
    {
        $userId = Auth::user()->id;
        return Project::where('user_id', $userId)->with('members.user', 'owner')->take(10)->get();
    }
    public function getAssignedTasks()
    {
        $userId = Auth::user()->id;
        $now = Carbon::now();
        return Task::where('assigned_id', $userId)
            ->whereBetween('due_date', [$now, $now->copy()->addDays(7)])
            ->where('status_id', '!=', 5)
            ->with('user', 'assignee', 'status')
            ->get();
    }
    
    private function getGreeting(): string
    {
        $user = Auth::user();
        $currentHour = Carbon::now()->format('H');
        $greeting = 'Salam aleikum, ' . $user->name . '. ';

        if ($currentHour < 12) {
            $greeting .= 'Good morning!';
        } elseif ($currentHour < 18) {
            $greeting .= 'Good afternoon!';
        } else {
            $greeting .= 'Good night!';
        }

        return $greeting;
    }

    public function with(): array
    {
        return [
            'tasks' => $this->tasks(),
            'greeting' => $this->greeting,
            'projects' => $this->projects,
            'assignedTasks' => $this->assignedTasks,

        ];
    }
};
?>


<div>
    <x-header title="Dashboard" separator progress-indicator>
        <div class="flex items-center justify-between">
            <div>
                <h1>{{ $greeting }} 👋</h1>
            </div>
        </div>
    </x-header>
    
    <div class="grid gap-5 pt-5 lg:grid-cols-4 lg:gap-8">
        <x-stat :value="$tasks['taskCount']" title="Tasks" icon="o-clipboard-document-list" class="shadow" />
        <x-stat :value="$tasks['taskPending']" title="Pending" icon="o-document-check" class="truncate shadow text-ellipsis" />
        <x-stat :value="$tasks['taskTerminated']" title="Terminated" icon="o-document-duplicate" class="truncate shadow text-ellipsis" />
        <x-stat :value="$tasks['taskDelayed']" title="Delayed" icon="o-minus-circle" class="truncate shadow text-ellipsis" />
    </div>

    <div class="grid grid-cols-1 gap-6 pt-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <!-- Projects Section -->
          <!-- Projects Section -->
          <div class="col-span-1 p-6 bg-white shadow-md lg:col-span-2 xl:col-span-3">
            <h2 class="mb-4 text-lg font-semibold">Projects</h2>
            <div class="overflow-x-auto">
                @if ($projects->isEmpty())
                <div class="flex items-center justify-center gap-10 mx-auto">
                    <div>
                        <img src="/images/no-results.png" width="300" />
                    </div>
                    <div class="text-lg font-medium">
                        Desole {{ Auth::user()->name }}, Pas des Project.
                    </div>
                </div>
                @else
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2">Project Name</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Created On</th>
                                <th class="py-2">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                <tr class="border-t">
                                    <td class="py-2">{{ Str::limit($project->name, 20) }}</td>
                                    <td class="py-2">
                                        <p>{{ $project->status->name }}</p>
                                    </td>
                                    <td class="py-2">{{ $project->created_at->format('d/m/Y') }}</td>
                                    <td class="py-2">{{ $project->duration }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        <!-- Calendar Section -->
        <div class="p-6 ">
            <h2 class="mb-4 text-lg font-semibold">Calendar</h2>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">{{ now()->format('F Y') }}</span>
               
            </div>
            <div class="grid grid-cols-7 gap-2 mt-4 text-center">
                @php
                    $events = [];
                    foreach ($projects as $project) {
                        $events[] = [
                            'label' => Str::limit($project->name, 20),
                            'description' => 'Start Date',
                            'css' => '!bg-green-200',
                            'date' => $project->start_date,
                        ];
                        $events[] = [
                            'label' => Str::limit($project->name, 20),
                            'description' => 'Due Date',
                            'css' => '!bg-red-200',
                            'date' => $project->due_date,
                        ];
                    }
                @endphp
                <x-calendar :events="$events" />
            </div>
        </div>
   

    
        <!-- Tasks Section -->
        <div class="col-span-1 p-6 bg-white shadow-md lg:col-span-2 xl:col-span-3">
            <h2 class="mb-4 text-lg font-semibold">Upcoming Tasks in Week</h2>
            @if ($assignedTasks->isEmpty())
            <div class="flex items-center justify-center gap-10 mx-auto">
                <div>
                    <img src="/images/no-results.png" width="300" />
                </div>
                <div class="text-lg font-medium">
                    Desole {{ Auth::user()->name }}, Pas des Tasks On Upcoming Days.
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($assignedTasks as $task)
                    <div class="p-4 rounded-lg bg-gray-50">
                        <p class="text-sm font-semibold"> {{ $task->name }} </p>
                        <p class="text-sm text-gray-600"> Made By ➡ {{ $task->user->name }}  </p>
                        <p class="text-sm text-gray-600">{{ $task->assignee->name }} ⬅</p>
                        <div class="flex justify-between mt-2 text-sm">
                            <span>{{ $task->start_date }} - {{ $task->due_date }}</span>
                          
                            <x-badge :value="$task->status->name" :class="$task->status->color" />
                                                </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    
        <!-- Messages Section -->
        <div class="p-6 bg-white shadow-md">
            <h2 class="mb-4 text-lg font-semibold">Messages</h2>
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/40" class="rounded-full" alt="User 1">
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Sharan</p>
                        <p class="text-sm text-gray-600">Lorem ipsum is simply dummy text</p>
                    </div>
                    <span class="text-xs text-gray-600">6:43 pm</span>
                </div>
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/40" class="rounded-full" alt="User 2">
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Amritha</p>
                        <p class="text-sm text-gray-600">Lorem ipsum is simply dummy text</p>
                    </div>
                    <span class="text-xs text-gray-600">3:35 pm</span>
                </div>
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/40" class="rounded-full" alt="User 3">
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Anand</p>
                        <p class="text-sm text-gray-600">Lorem ipsum is simply dummy text</p>
                    </div>
                    <span class="text-xs text-gray-600">11:00 am</span>
                </div>
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/40" class="rounded-full" alt="User 4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Aravind</p>
                        <p class="text-sm text-gray-600">Lorem ipsum is simply dummy text</p>
                    </div>
                    <span class="text-xs text-gray-600">8:00 am</span>
                </div>
            </div>
        </div>
    </div>
</div>


