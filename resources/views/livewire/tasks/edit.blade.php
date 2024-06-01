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



   

        <div class="grid gap-2 lg:grid-cols-2">
            {{-- DETAILS --}}
                <div class="grid gap-5 lg:px-3" wire:key="details">
                                                                <!-- name update views  -->
<x-card>
                    <x-input label="Task Name" wire:model.defer="name" placeholder="Enter task name" :value="$task->name" wire:keydown.enter="changeTaskName" />
</x-card>
                                                                    <!-- Description update views  -->
        

                        <form wire:submit.prevent="changeTaskDescription">

                    <x-textarea
                        label="Description"
                        wire:model="description"
                        rows="5" 
                        inline 
                    />
                    <div class="mt-4">
                        <x-button type="submit" class="bg-blue-500">Update Description</x-button>
                    </div>
                </form>

                                                            <!-- Status update views  -->
                                                            <label for="">Status</label>         

                <select wire:model="status_id" label="Status" wire:change="changeStatus" id="status" name="status" class="block w-full p-8 border-0 border-solid divide-y divide-blue-200 rounded shadow hover:border-dotted" >
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
                                                            <!-- Priority update views  -->
                                                            <label for="">Priority</label>         

             <select wire:model="priority_id" label="Priority" wire:change="changePriority" id="priority" name="priority" class="block w-full p-8 border-0 border-solid divide-y divide-blue-200 rounded shadow hover:border-dotted" >
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                    @endforeach
                </select>
                                                            <!-- Start_date update views  -->
                                                            

                <div class="p-6 mt-6 bg-white rounded-lg shadow">
                @php
                $config1 = ['altFormat' => 'd/m/Y'];
               @endphp
                 <x-datepicker label="Start Date" wire:model.defer="start_date"  wire:change="updateStartDate($event.target.value)" :config="$config1" />
            </div>
                                                        <!-- Due_date update views  -->

            <div class="p-6 mt-6 bg-white rounded-lg shadow">
                <x-datepicker label="Due Date" wire:model.defer="due_date" wire:change="updateEndDate($event.target.value)" :config="$config1" />
            </div>

                                                         <!-- Assignee update views  -->
                                                         <label for="">Assigned</label>         


            <select wire:model="assignee_id" label="Assigned" wire:change="changeAssignee" id="assignee" name="assignee" class="w-full p-8 border-0 border-solid divide-y divide-blue-200 rounded shadow p-8block hover:border-dotted" >
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

                                            <!-- category update views  -->

                                            <label for="">Category</label>         


            <select wire:model="category_id" label="Category" wire:change="changeCategory" id="category" name="category" class="block w-full p-8 border-0 border-solid divide-y divide-blue-200 rounded shadow hover:border-dotted" >
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select> 
            
                                <!-- project update views  -->
                                <label for="">Project</label>         


            <select wire:model="project_id" label="Project" wire:change="changeProject" id="project" name="project" class="block w-full p-8 border-0 border-solid divide-y divide-blue-200 rounded shadow hover:border-dotted" >
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
                  

                </div>
               
            

            <div class="grid content-start gap-8">
                <x-card title="Files" separator>
                    <form action="{{ route('file.store', $task) }}" class="p-4 mb-6 bg-white rounded-lg shadow" enctype="multipart/form-data" method="post">
                        @csrf
                        <input wire:model="uploadedFiles" type="file" name="uploadedFiles[]" multiple>
                     <div>
                        <button type="submit" class="p-2 bg-green-200 rounded">Upload Files</button>
                     </div>
                    </form>

                    @if($task->file->isNotEmpty())
                    @foreach ($task->file as $file)
                    <div class="flex w-full items-center justify-between rounded-2xl bg-white p-3 shadow-3xl shadow-shadow-500 dark:!bg-navy-700 dark:shadow-none">
                        <div class="flex items-center">
                            
                        <div class="">
                            <img
                            class="h-[83px] w-[83px] rounded-lg"
                            src="https://cdn.dribbble.com/users/1397292/screenshots/16139947/media/8e4fd02616f0e1053030b7c9bc9559b5.png?resize=1600x1200&vertical=center"
                            alt=""
                            />
                        </div>
                        <div class="ml-4">
                            <p class="text-base font-medium text-navy-700 dark:text-white">
                            {{ $file->name }}
                            </p>
                            @php
                             $sizeInMB = round($file->size / (1024 * 1024), 2); 
                        @endphp
                            <p class="mt-2 text-sm text-gray-600">
                                {{ $sizeInMB }} MB
    
                                <a
                                class="ml-1 font-medium text-brand-500 hover:text-brand-500 dark:text-white"
                                href=" "
                            >
                            </a>
                            </p>
                        </div>
                        </div>
                        <div class="flex items-center justify-center mr-4 text-gray-600 dark:text-white">
                            <a href="{{ Storage::url($file->file_path) }}" download="{{ $file->name }}" class="bg-white">
                                <x-icon name="m-arrow-down-on-square" />
                            </a>|
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="bg-white">   <x-icon name="o-eye" /></a>|
                            <x-button 
                            wire:click="delete({{ $file->id }})" 
                            class="btn-error" 
                            wire:confirm="Are you sure?" 
                            icon="o-trash"  
                            spinner 
                            responsive
                        />

                        </div>
                    </div>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="2" class="text-center">No files available.</td>
                    </tr>
                    @endif
                    <!-- Add file upload inputs and logic here if needed -->
                </x-card>

                {{-- MORE IMAGES --}}
                <x-card title="Comments" separator>
                    <x-form>
                    <div class="mb-6 rounded-lg ">
                        <div class="border-b "></div>
                        <div class="flex w-full ">
                            <div class="flex flex-row mx-5 mt-3 text-xs">
                                <div class="flex items-center mb-2 mr-4 font-normal text-gray-700 rounded-md">Comments:<div class="ml-1 text-gray-400 text-ms"> {{ $task->comments->count() }}</div></div>
                            </div>
                        </div>
                        @if($task->comments->count() > 0)
                        @foreach ($task->comments as $comment)
                        <div class="flex p-4 antialiased text-black">
                            <img class="w-8 h-8 mt-1 mr-2 rounded-full " src="https://cdn.dribbble.com/users/2071065/screenshots/5746865/dribble_2-01.png">
                            <div>
                                <div class="bg-gray-100 rounded-lg px-4 pt-2 pb-2.5">
                                    <div class="text-sm font-semibold leading-relaxed">{{ $comment->user->name }}</div>
                                    <div class="text-xs leading-snug md:leading-normal">{{ $comment->comment }}.</div>
                                </div>
                                <div class="text-xs  mt-0.5 text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                                
                            </div>
                        </div>
                        @endforeach
                        @else
                            <p>No comments available.</p>
                        @endif
                        
        
                        <div class="relative flex items-center self-center w-full max-w-xl p-4 overflow-hidden text-gray-600 focus-within:text-gray-400">
                            <img class="object-cover w-10 h-10 mr-2 rounded-full shadow cursor-pointer" alt="User avatar" src="https://cdn.dribbble.com/users/2071065/screenshots/5746865/dribble_2-01.png">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-6">
                                <button type="submit" wire:click="saveComment" class="p-1 focus:outline-none focus:shadow-none hover:text-blue-500" spinner>
                                <svg class="w-6 h-6 text-gray-400 transition duration-300 ease-out hover:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <x-icon name="o-paper-airplane"  />
                                </svg>
                                </button>
                            </span>
                                <input type="search" wire:model="comment" class="w-full py-2 pl-4 pr-10 text-sm placeholder-gray-400 bg-gray-100 border border-transparent appearance-none rounded-tg" style="border-radius: 25px" placeholder="Post a comment..." autocomplete="off">
                            </div>
                    </div>
                    </x-form>
                </x-card>
            </div>
        </div>

     
</div>
