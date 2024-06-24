<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Note;
use App\Models\Task;
use App\Models\Status;
use App\Models\Subtask;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use App\Models\File;
use App\Models\History;
use App\Models\Category;
use App\Mail\TaskCreatedMail;

use App\Models\Priority;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;
use App\Mail\TaskCommented;
use Illuminate\Support\Facades\Mail;

new class extends Component {
    use Toast;
    public bool $myModal2 = false;

    public Task $task;
    public $name;
    public $assigned_id;
    public $description;
    public $status_id;
    public $project_id;
    public $priority_id;
    public $category_id;
    public $start_date;
    public $due_date;
    public $files = []; 
    public $tags = [];
    public $comment;
    public $selectedTaskId;
    public $subtaskName;
    public $completedSubtasksCount;
    public function mount(Task $task)
    {
        $this->task = $task;
        $this->name = $task->name;
        $this->description = $task->description;
        $this->start_date = $task->start_date;
        $this->due_date = $task->due_date;
        $this->status_id = $task->status_id;
        $this->priority_id = $task->priority_id;
        $this->category_id = $task->category_id;
        $this->assigned_id = $task->assigned_id;
        $this->project_id = $task->project_id;
        $this->tags= $task->tags;
        $this->file = $task->file;
        $this->comments = $task->comments;
        $this->completedSubtasksCount = $task->subtasks()->where('completed', true)->count();

    }

    public function statuses(): Collection
    {
        return Status::orderBy('name')->get();
    }
    public function projects(): Collection
    {
        return Project::orderBy('name')->get();
    }

    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function priorities(): Collection
    {
        return Priority::orderBy('name')->get();
    }
    public function users(): Collection
    {
        return User::orderBy('name')->get();
    }
    

    public function saveTask()
{
    $validatedData = $this->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'category_id' => 'required|exists:categories,id',
        'tags' => 'required',
        'priority_id' => 'required|exists:priorities,id',
        'project_id' => 'required|exists:projects,id',
        'status_id' => 'required|exists:statuses,id',
        'assigned_id' => 'required|exists:users,id',
        'start_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:start_date',
    ]);

    // Fill the task model with validated data
    $this->task->fill($validatedData);

    // Save the task
    $this->task->save();

    $this->toast(
            type: 'warning',
            title: 'Mise A jour!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-start',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );

}
public function delete($fileId): void
    {
    
        $file = File::find($fileId);

if ($file) {
    // Delete the file from storage
    Storage::delete($file->file_path);

    // Delete the file record from the database
    $file->delete();

    $this->toast(
        type: 'success',
        title: 'File Deleted!',
        description: null,
        position: 'toast-bottom toast-start',
        icon: 'o-trash',
        timeout: 3000
    );
} else {
    $this->toast(
        type: 'error',
        title: 'Error!',
        description: 'File not found.',
        position: 'toast-bottom toast-start',
        icon: 'o-alert-circle',
        timeout: 3000
    );
}
    }

    public function saveComment()
{
    $validatedData = $this->validate([
        'comment' => 'required|string|max:255',
    ]);

    $this->task->comments()->create([
        'comment' => $this->comment,
        'user_id' => Auth::id(),
        'department_id' => Auth::user()->department_id,
        'date' => now(),
    ]);


    $this->comment = '';
    $this->task->load('comments'); 

    
$taskOwner = $this->task->user; 
$taskAssignee = $this->task->assignee; 


$commentText = $validatedData['comment'];



if ($taskOwner) {
    Mail::to($taskOwner->email)->send(new TaskCommented($this->task, $commentText));
}


if ($taskAssignee && $taskAssignee->id !== $taskOwner->id) {
    Mail::to($taskAssignee->email)->send(new TaskCommented($this->task, $commentText));
} 
    $this->comments = $this->task->comments()->orderBy('created_at')->get();

    $this->toast('success', 'Comment added successfully.');
}
    public function deleteComment($commentId)
{
    $comment = Comment::find($commentId);

    if ($comment && $comment->user_id === Auth::id()) {
        $comment->delete();
        $this->toast('success', 'Comment deleted successfully.');
        $this->comment = $this->task->comment()->orderBy('created_at')->get();

    } else {
        $this->toast('error', 'Unable to delete comment.');
    }
}

//  update task name //
public function changeTaskName()
{
    

    $this->task->update(['name' => $this->name]);

    

    $this->toast(
            type: 'warning',
            title: 'Mise A jour, Nom Du Task!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Name updated', $this->task->id, Task::class);

}

// update Task Description 
public function changeTaskDescription()
{
    $this->task->update(['description' => $this->description]);


    $this->toast(
            type: 'warning',
            title: 'Mise A jour, Description!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Description updated', $this->task->id, Task::class);

}


// update task status //
public function changeStatus()
    {

        $this->task->update(['status_id' => $this->status_id]);


        $this->toast(
            type: 'warning',
            title: 'Mise A jour,le Status !',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Status updated', $this->task->id, Task::class);


    }
// task priority_id //

    public function changePriority()
    {
        $this->task->update(['priority_id' => $this->priority_id]);

        $this->toast(
            type: 'warning',
            title: 'Mise A jour, Priorite!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Priority updated', $this->task->id, Task::class);

    }


    // update task start update  //

    
    public function updateStartDate($newStartDate)
{
    $startDate = Carbon::parse($newStartDate);

    $this->task->update(['start_date' => $startDate]);


    $this->toast(
        type: 'warning',
        title: 'Start Date Updated!',
        description: null,
        position: 'toast-bottom toast-end',
        icon: 'o-information-circle',
        css: 'alert-warning',
        timeout: 3000,
        redirectTo: null
    );

    $this->logHistory('Start date updated', $this->task->id, Task::class);
}




   // update task  due date  // 

public function updateEndDate($newEndDate)
{
    $endDate = Carbon::parse($newEndDate);
    
    $this->task->update(['due_date' => $endDate]);


    $this->toast(
        type: 'warning',
        title: 'End Date Updated!',
        description: null,
        position: 'toast-bottom toast-end',
        icon: 'o-information-circle',
        css: 'alert-warning',
        timeout: 3000,
        redirectTo: null
    );

    $this->logHistory('End date updated', $this->task->id, Task::class);
}

// update Task Assigned //
public function changeAssignee()
{
    $this->task->update(['assigned_id' => $this->assigned_id]);
    $assigned = User::findOrFail($this->assigned_id);
    $creator = auth()->user();

    // Corrected line: Replace '$task' with '$this->task'
    Mail::to($assigned->email)->send(new TaskCreatedMail([
        'task' => $this->task, // Corrected variable
        'assigned' => $assigned,
        'creator' => $creator,
        'project' => $this->task->project, // Assuming $this->task->project is accessible
    ]));
    
    $this->toast(
        type: 'warning',
        title: 'Mise A jour, Assignee!',
        description: null,
        position: 'toast-bottom toast-end',
        icon: 'o-information-circle',
        css: 'alert-warning',
        timeout: 3000,
        redirectTo: null
    );
    $this->logHistory('Assignee updated', $this->task->id, Task::class);
}

// update task priority_id //

public function changeCategory()
    {
        $this->task->update(['category_id' => $this->category_id]);

        $this->toast(
            type: 'warning',
            title: 'Mise A jour, Priorite!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Category updated', $this->task->id, Task::class);

    }

    // make subtask //
    public function addSubtask()
    {
        $this->validate([
            'subtaskName' => 'required|string|max:255',
        ]);

        Subtask::create([
            'task_id' => $this->task->id,
            'name' => $this->subtaskName,
            'completed' => false,
        ]);

        $this->subtaskName = '';
    
    }
// checked the subtask //
public function toggleSubtaskCompletion(Subtask $subtask)
    {
        $subtask->completed = !$subtask->completed;
        $subtask->save();
        $this->task->refresh();
        $this->completedSubtasksCount = $this->task->subtasks()->where('completed', true)->count();
    }
//  update task project_id  //
    public function changeProject()
    {
        $this->task->update(['project_id' => $this->project_id]);

        $this->toast(
            type: 'warning',
            title: 'Mise A jour, Project!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Project updated', $this->task->id, Task::class);

    }



private function logHistory($action, $modelId, $modelType)
    {
        History::create([
            'action' => $action,
            'model_id' => $modelId,
            'model_type' => $modelType,
            'date' => now(),
            'name' => $this->name, 
            'department_id' => Auth::user()->department_id,
            'user_id' => Auth::id(),
        ]);
    }
    public function refreshTasks()
    {
        $this->task->refresh();
    }

    public function with(): array
    {
        return [
            'statuses' => $this->statuses(),
            'categories' => $this->categories(),
            'priorities' => $this->priorities(),
            'projects' => $this->projects(),
            'users' => $this->users(),
            'task' => $this->task->load('subtasks')
        ];
    }
}; ?>
<div class="w-full m-10 rounded-lg shadow-lg">
    <div class="flex flex-col p-6 border-b border-gray-200 lg:flex-row">
        <!-- Task Details Section -->
        <div class="w-full mb-6 lg:w-2/3 lg:pr-6 lg:mb-0">
            <div class="mb-4 text-lg font-semibold">{{ $task->name }}</div>
            <div class="mb-4 text-sm text-gray-500">{{ $task->description }}</div>
            
            <ul class="mb-6">
                <li class="flex items-center mb-2">
                    <span class="p-2">Category: {{ $task->category->name }}</span>
                </li>
                <li class="flex items-center mb-2">
                    <span class="p-2">Priority: {{ $task->priority->name }}</span>
                </li>
            </ul>

            <div class="flex items-center mb-6">
                <span class="text-sm text-gray-500">Labels</span>
                <span class="px-2 py-1 ml-2 text-xs text-green-600 bg-green-100 rounded-full">Finance</span>
            </div>

            <div class="text-sm text-gray-500 ">Subtasks</div>
            <hr>
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-xl font-bold">{{ $completedSubtasksCount }} out of {{ $task->subtasks->count() }}</h1>
                        <button class="text-blue-500">Check all</button>
                    </div>
                    <div>
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Name</th>
                                    <th class="px-4 py-2">Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($task->subtasks as $subtask)
                                    <tr>
                                        <td class="px-4 py-2 border">{{ $subtask->name }}</td>
                                        <td class="px-4 py-2 text-center border">
                                            <input type="checkbox" 
                                                   wire:click="toggleSubtaskCompletion({{ $subtask->id }})"
                                                   class=""
                                                   {{ $subtask->completed ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Repeat above structure for remaining items -->
                    </div>
                
            
            <button wire:click="$set('selectedTaskId', {{ $task->id }})" @click="$wire.myModal2 = true">Add Subtask</button>
            <x-modal wire:model="myModal2" title="Add Subtask" class="backdrop-blur">
                <x-slot:actions>
                    <input type="text" wire:model="subtaskName" placeholder="New subtask" class="w-full px-4 py-2 mb-4 border rounded">
                    <x-button wire:click="addSubtask" label="Add Subtask" />
                </x-slot:actions>
            </x-modal>
        </div>

        <!-- Task Sidebar Section -->
        <div class="w-full lg:w-1/3 lg:pl-6">
            <div class="mb-6">
                <div class="mb-2 text-sm text-gray-500">Assignee</div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 mr-2 bg-gray-200 rounded-full">H</div>
                    <div class="text-sm">{{ $task->assignee->email }}</div>
                </div>
            </div>
            @php
            $config1 = ['altFormat' => 'd/m/Y'];
           @endphp
            <div class="mb-6">
                <div class="mb-2 text-sm text-gray-500">Dates</div>
                <div class="flex flex-col">
                    <div class="rounded-lg shadow">
                        <x-datepicker label="Start Date" wire:model.defer="start_date"  wire:change="updateStartDate($event.target.value)" :config="$config1" />

                    </div>
                    <div class="rounded-lg shadow">
                        <x-datepicker label="Due Date" wire:model.defer="due_date" wire:change="updateEndDate($event.target.value)" :config="$config1" />
                    </div>
                </div>
            </div>
            <div class="mb-6">
                <div class="mb-2 text-sm text-gray-500">Status</div>
                    <select wire:model="status_id" label="Status" wire:change="changeStatus" id="status" name="status" class="w-full p-2 text-sm border border-gray-300 rounded-lg">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="mb-6">
                <div class="mb-2 text-sm text-gray-500">Project</div>
                    <span>{{ $task->project->name }}</span>
            </div>
          <!--  <div class="mb-6">
                <div class="mb-2 text-sm text-gray-500">2 following</div>
                <button class="text-sm text-blue-600">Share</button>
            </div>
            <div class="flex flex-col space-y-2">
                <button class="text-sm text-gray-600">Archive</button>
                <button class="text-sm text-red-600">Delete</button>
            </div>-->
        </div>
    </div>
    <x-card>
        <x-tabs wire:model="selectedTab">
            <x-tab name="users-tab">
                <x-slot:label>
                </x-slot:label>
            </x-tab>
            <x-tab name="tricks-tab" label="Comments">
                @if($task->comments->count() > 0)
                @foreach ($task->comments as $comment)
                    <div class="flex p-4 antialiased text-black">
                        <img class="w-8 h-8 mt-1 mr-2 rounded-full" src="https://cdn.dribbble.com/users/2071065/screenshots/5746865/dribble_2-01.png">
                        <div>
                            <div class="bg-gray-100 rounded-lg px-4 pt-2 pb-2.5">
                                <div class="text-sm font-semibold leading-relaxed">{{ $comment->user->name }}</div>
                                <div class="text-xs leading-snug md:leading-normal">{{ $comment->comment }}</div>
                            </div>
                            <div class="text-xs mt-0.5 text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <p>No comments available.</p>
            @endif
            
            <div class="relative flex items-center self-center w-full max-w-xl p-4 overflow-hidden text-gray-600 focus-within:text-gray-400">
                <img class="object-cover w-10 h-10 mr-2 rounded-full shadow cursor-pointer" alt="User avatar" src="https://cdn.dribbble.com/users/2071065/screenshots/5746865/dribble_2-01.png">
                <span class="absolute inset-y-0 right-0 flex items-center pr-6">
                    <button type="submit" wire:click="saveComment" class="p-1 focus:outline-none focus:shadow-none hover:text-blue-500">
                        <svg class="w-6 h-6 text-gray-400 transition duration-300 ease-out hover:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <x-icon name="o-paper-airplane" />
                        </svg>
                    </button>
                </span>
                
                <input type="search" wire:model="comment" class="w-full py-10 pl-4 pr-10 text-sm placeholder-gray-400 bg-gray-100 border border-transparent rounded-lg appearance-none" style="border-radius: 25px" placeholder="Post a comment..." autocomplete="off">
            </div>
            </x-tab>
            <x-tab name="musics-tab" label="Musics">
                <div>Musics</div>
            </x-tab>
        </x-tabs>
    </x-card>
</div>
