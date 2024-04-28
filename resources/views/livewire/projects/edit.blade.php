<?php

use App\Models\Comment;
use App\Models\File;
use App\Models\History;
use App\Models\Project;
use App\Models\Status;
use App\Models\User;
use App\Models\Category;
use App\Models\Member;
use App\Models\Priority;
use App\Models\Task; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;


use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public Project $project;
    public $name;
    public $selectedUser = [];
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
    public $search = '';

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->start_date = $project->start_date;
        $this->due_date = $project->due_date;
        $this->status_id = $project->status_id;
        $this->priority_id = $project->priority_id;
        $this->category_id = $project->category_id;
        $this->assigned_id = $project->assigned_id;
        $this->tags = $project->tags;
      
    }
   

    // Other methods...

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
    public function tasks(): LengthAwarePaginator
{
    $perPage = 10;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $items = $this->project->tasks()->paginate($perPage, ['*'], 'page', $currentPage);

    return $items;
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
    
    public function saveProject()
{
    $validatedData = $this->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'category_id' => 'required|exists:categories,id',
        'tags' => 'required',
        'priority_id' => 'required|exists:priorities,id',
        'status_id' => 'required|exists:statuses,id',
        'start_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:start_date',
    ]);

    // Fill the task model with validated data
    $this->project->fill($validatedData);

    // Save the task
    $this->project->save();

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
public function searchMulti($search)
{
    // Update the search property
    $this->search = $search;

    // Return the filtered users based on the search query
    return User::where('name', 'like', "%{$search}%")
        ->take(10)
        ->get();
}

public function addedMember()
{
    // Validate if any user is selected
    $this->validate([
        'selectedUser' => 'required|exists:users,id' // Make sure a user is selected and exists
    ]);

    // Check if the user is already a member
    if (!$this->project->members()->where('user_id', $this->selectedUser)->exists()) {
        // Create a new Member record for the selected user
        Member::create([
            'model_id' => $this->project->id,
            'model_type' => Project::class,
            'user_id' => $this->selectedUser,
            'department_id' => Auth::user()->department_id,
            'date' => Carbon::now(),
        ]);

        // Load the updated project members
        $this->project->load('members.user');
        // Reset the list of non-member users
       // $this->loadNonMemberUsers();

        // Show success message
        $this->toast('success', 'Member added successfully.');

        // Send email notification to the added user
      //  $user = User::findOrFail($this->selectedUser);
      //  Mail::to($user->email)->send(new MemberAddedToProjectMail($this->project, $user));

        // Clear the selected user after adding
        $this->selectedUser = null;
    } else {
        // Show error message if the user is already a member
        $this->toast('error', 'This user is already a member of the project.');
    }
}



    public function with(): array
    {
        return [
            'statuses' => $this->statuses(),
            'categories' => $this->categories(),
            'priorities' => $this->priorities(),
            'projects' => $this->projects(),
            'users' => $this->users(),
            'headers' => $this->headers(), 
            'tasks' => $this->tasks(), // Add this line


        ];
    }
    public function headers(): array
{
    return [
            ['key' => 'preview', 'label' => '', 'class' => 'w-14', 'sortable' => false],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'status.name', 'label' => 'Status', 'sortBy' => 'status_name', 'class' => 'hidden lg:table-cell'],
            ['key' => 'category.name', 'label' => 'Category', 'sortBy' => 'category_name', 'class' => 'hidden lg:table-cell'],
            ['key' => 'priority.name', 'label' => 'Priority', 'sortBy' => 'priority_name', ],
            ['key' => 'user.name', 'label' => 'assignee_id', 'sortBy' => 'user_name', ],

        ];
}

};
 ?>

 
<div>
    <div class="grid content-start gap-8">

    <x-header :title="$project->name" separator>
        <x-slot:actions>
        </x-slot:actions>
    </x-header>

    <div class="grid gap-8 lg:grid-cols-2">
        {{-- CUSTOMER --}}
        <x-form wire:submit.prevent="saveProject">

            @csrf <!-- Add CSRF token -->
        
                    {{-- DETAILS --}}
                    <x-card title="Details" separator>
                        <div class="grid gap-5 lg:px-3" wire:key="details">
                            <x-input label="Name" wire:model="name" />
                            <x-textarea
                                label="Description"
                                wire:model="description"
                                rows="5"
                                inline
                            />
                            <x-choices-offline label="Status" wire:model="status_id" :options="$statuses" single searchable />
                            <x-choices-offline label="Categories" wire:model="category_id" :options="$categories" single searchable  />
                            <!-- Assuming $task->tags returns an array of tag names -->
                            @php
            $config1 = ['altFormat' => 'd/m/Y'];
        @endphp
                            <x-datepicker label="Start Date" wire:model="start_date" icon-right="o-calendar" :config="$config1" />
                            <x-datepicker label="Due Date" wire:model="due_date" icon-right="o-calendar" :config="$config1" />
                            <x-tags label="Tags" wire:model="tags" icon="o-home" />
        
                        </div>
                        <x-slot:actions>
                            <x-button label="Cancel" link="/projects" />
                            <x-button label="Save Changes" spinner="saveTask" type="submit" icon="o-paper-airplane" class="btn-primary" />
                        </x-slot:actions>
                    </x-card>
                </x-form>
        {{-- SUMMARY --}}
        {{-- SUMMARY --}}
         <x-form wire:submit.prevent="addedMember">
            <x-choices-offline
    label="Member"
    wire:model="user_id"
    :options="$users"
    searchable />
            <x-button wire:click.prevent="addedMember" spinner="addedMember" class="mt-2" icon="o-user-plus"  />

        </x-form>

    </div>
{{-- ITEMS --}}
<div class="grid content-start gap-8">
    <x-card title="tasks" separator>
           
        <x-table :headers="$headers" :rows="$tasks" />

       
    </x-card>
</div>


        </div>
</div>
