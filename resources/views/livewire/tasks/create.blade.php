<?php

use App\Models\Brand;
use App\Models\Task;
use App\Models\User;
use App\Models\Priority;
use App\Traits\HasCssClassAttribute;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskCreatedMail;


new class extends Component {
    use Toast, HasCssClassAttribute;

    #[Rule('required')]
    public string $name = ''; 
    #[Rule('required')]
    public string $description = ''; 
    #[Rule('required|date')]
    public string $start_date = ''; 
    #[Rule('required')]
    public string $due_date = '';
    public int $assigned_id = 0; // Set a default value
    public int $project_id = 0; // Set a default value
    public int $priority_id = 0; // Set a default value

   


    public bool $show = false;

    public string $label = 'Create';

    public function save(): void
    { 
        $this->validate([
        'name' => 'required',
        'description' => 'required',
        'start_date' => 'required|date',
        'due_date' => 'required|date',
        'assigned_id' => 'required', // Ensure assigned_id is provided
        'priority_id' => 'required', // Ensure priority_id is provided
    ]);

    $task = Task::create([
        'name' => $this->name,
        'description' => $this->description,
        'start_date' => $this->start_date,
        'due_date' => $this->due_date,
        'user_id' => Auth::user()->id, // Example user_id assignment, adjust as needed
        'department_id' => 4, // Example department_id assignment, adjust as needed
        'assigned_id' => $this->assigned_id, // Assign assigned_id
        'priority_id' => $this->priority_id, // Assign priority_id
    ]);
    $assigned = User::findOrFail($this->assigned_id);
    $creator = auth()->user();

    Mail::to($assigned->email)->send(new TaskCreatedMail([
        'task' => $task,
        'assigned' => $assigned,
        'creator' => $creator,
        'project' => $task->project, 
    ]));

    $this->show = false;
    $this->resetExcept('label', 'class');
    $this->dispatch('task-saved', id: $task->id);
    $this->success('Task created.');
    }

}; ?>

<div>
    <x-button :label="$label" @click="$wire.show = true" icon="o-plus" class="btn-primary {{ $class }}" responsive />

    {{-- This component can be used inside another forms. So we teleport it to body to avoid nested form submission conflict --}}
    <template x-teleport="body">
        <x-modal wire:model="show" title="Create Brand">
            <hr class="mb-5" />
            <x-form wire:submit="save">
                <x-input label="Name" wire:model="name" />
                <x-textarea
    label="Description"
    wire:model="description"
    placeholder="..."
    hint="Max 1000 chars"
    rows="5"
    inline />
    @php
                    $config1 = ['altFormat' => 'd/m/Y'];
                    @endphp
                    <x-datepicker label="Start Date" wire:model="start_date" icon-right="o-calendar" :config="$config1" />
                    <x-datepicker label="Due Date" wire:model="due_date" icon-right="o-calendar" :config="$config1" />
                    @php
                   $priorities = App\Models\Priority::take(10)->get();
                   @endphp
                    <x-choices-offline label="Priority" wire:model="priority_id" :options="$priorities" single searchable></x-choices-offline>
                    @php
                   $users = App\Models\User::take(100)->get();
                   $projects = App\Models\Project::take(100)->get();

                   @endphp
                    <x-select label="Assignee" icon="o-user" :options="$users" wire:model="assigned_id" />
                    <x-select label="Project" icon="o-user" :options="$projects" wire:model="project_id" />


                <x-slot:actions>
                    <x-button label="Cancel" @click="$wire.show = false" />
                    <x-button label="Save" icon="o-paper-airplane" class="btn-primary" type="submit" />
                </x-slot:actions>
            </x-form>
        </x-modal>
    </template>
</div>
