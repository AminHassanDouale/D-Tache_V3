<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Note;
use App\Models\Task;
use App\Models\Status;
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
            position: 'toast-bottom toast-end',    
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


    public function with(): array
    {
        return [
            'statuses' => $this->statuses(),
            'categories' => $this->categories(),
            'priorities' => $this->priorities(),
            'projects' => $this->projects(),
            'users' => $this->users(),
        ];
    }
}; ?>
<div>
    <main class="p-6 bg-gray-100">
        <div class="mx-auto max-w-7xl">
            <h1 class="mb-4 text-2xl font-bold">Documents</h1>
            
            <div class="flex items-center mb-6">
                <div class="flex-1">
                    <select class="p-2 border-gray-300 rounded">
                        <option>All Departments</option>
                    </select>
                </div>
                <div class="flex space-x-4">
                    <button class="text-gray-600 hover:text-gray-800">Sort</button>
                    <button class="text-gray-600 hover:text-gray-800">Filter</button>
                    <div class="flex -space-x-2">
                        <img src="https://via.placeholder.com/32" class="w-8 h-8 border-2 border-white rounded-full" alt="User 1">
                        <img src="https://via.placeholder.com/32" class="w-8 h-8 border-2 border-white rounded-full" alt="User 2">
                        <img src="https://via.placeholder.com/32" class="w-8 h-8 border-2 border-white rounded-full" alt="User 3">
                        <div class="flex items-center justify-center w-8 h-8 text-gray-700 bg-gray-200 border-2 border-white rounded-full">+6</div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center mb-4 space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.293 9.293a1 1 0 011.414 0L12 10.586l2.293-2.293a1 1 0 011.414 1.414L13.414 12l2.293 2.293a1 1 0 01-1.414 1.414L12 13.414l-2.293 2.293a1 1 0 01-1.414-1.414L10.586 12 8.293 9.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </div>
                    <p class="font-semibold text-gray-700">Finance</p>
                </div>
            </div>
    
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div class="p-4 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.293 9.293a1 1 0 011.414 0L12 10.586l2.293-2.293a1 1 0 011.414 1.414L13.414 12l2.293 2.293a1 1 0 01-1.414 1.414L12 13.414l-2.293 2.293a1 1 0 01-1.414-1.414L10.586 12 8.293 9.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Income Statement</p>
                                <p class="text-xs text-gray-500">17.08.20</p>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-4">
                        <span class="inline-block px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-full"># Sanctions actions</span>
                    </div>
                    <div class="mt-4 text-center">
                        <button class="text-sm font-medium text-blue-500 hover:underline">DETAILS</button>
                    </div>
                </div>
                
                <!-- Repeat similar blocks for Balance Sheet and Bank Statement -->
                
                <div class="p-4 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.293 9.293a1 1 0 011.414 0L12 10.586l2.293-2.293a1 1 0 011.414 1.414L13.414 12l2.293 2.293a1 1 0 01-1.414 1.414L12 13.414l-2.293 2.293a1 1 0 01-1.414-1.414L10.586 12 8.293 9.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Balance Sheet</p>
                                <p class="text-xs text-gray-500">17.08.20</p>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-4">
                        <span class="inline-block px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-full"># Notice</span>
                        <span class="inline-block px-2 py-1 text-xs font-medium text-gray-500 bg-gray-100 rounded-full"># News</span>
                    </div>
                    <div class="mt-4 text-center">
                        <button class="text-sm font-medium text-blue-500 hover:underline">DETAILS</button>
                    </div>
                </div>
    
                <div class="p-4 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full">
                                <img src="/images/support-us.png" width="300" class="mx-auto" />
                            </div>
                            <div class="flex items-start justify-start float-start">
                                
                                <p class="text-sm font-medium text-gray-700 ">Bank Statement</p>
                                <p class="text-xs text-gray-500">17.08.20</p>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-4">
                        <span class="inline-block"> <p class="text-sm font-medium text-gray-700 ">Bank Statement</p>
                            <p class="text-xs text-gray-500">17.08.20</p></span>
                    </div>
                    <div class="mt-4 text-center">
                        <button class="text-sm font-medium text-blue-500 hover:underline">DETAILS</button>
                    </div>
                </div>
    
                <div class="flex items-center justify-center p-4 border-2 border-gray-300 border-dashed rounded-lg">
                    <button class="flex flex-col items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>ADD DOCUMENT</span>
                    </button>
                </div>
            </div>
        </div>
    
    </main>
    
    </div>