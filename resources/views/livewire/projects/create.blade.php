<?php

use App\Actions\DeleteProductAction;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Project;
use App\Models\Priority;
use App\Models\Status;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Mary\Traits\WithMediaSync;

new class extends Component {
    use Toast, WithFileUploads, WithMediaSync;

    public $name, $description, $status_id, $category_id, $start_date, $due_date, $priority_id;
    public $tags = [];




    public function mount(): void
    {
        $this->library = new Collection();
    }

    #[On('brand-saved')]

    #[On('category-saved')]
    public function newCategory($id): void
    {
        $this->category_id = $id;
    }

    public function statuses(): Collection
    {
        return Status::orderBy('name')->get();
    }

    public function priorities(): Collection
    {
        return Priority::orderBy('name')->get();
    }

    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }
    public function save(): void
    {
        // Validate
        $validatedData = $this->validate([
            'status_id' => 'required',
            'category_id' => 'required',
            'priority_id' => 'required',
            'name' => 'required|string',
            'name' => 'nullable',
            'tags' => 'required|array',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'description' => 'required|string',
        ]);

        // Create the project
        $project = new Project();
        $project->status_id = $validatedData['status_id'];
        $project->category_id = $validatedData['category_id'];
        $project->name = $validatedData['name'];
        $project->priority_id = $validatedData['priority_id'];
        $project->tags = $validatedData['tags'];
        $project->start_date = $validatedData['start_date'];
        $project->due_date = $validatedData['due_date'];
        $project->description = $validatedData['description'];

        // Save project
        $project->save();

        // Display success message
        $this->success('Project created successfully.', redirectTo: '/projects');
    }



    public function with(): array
    {
        return [
            'statuses' => $this->statuses(),
            'categories' => $this->categories(),
            'priorities' => $this->priorities(),
        ];
    }
}; ?>

<div>
    <x-header title="Create project" separator />

    <x-form wire:submit="save">
        <div class="grid gap-8 lg:grid-cols-2">
            <x-card title="Details" separator>
                <div class="grid gap-3 lg:px-3" wire:key="details">
                    <x-input label="Name" wire:model="name" />
                    <x-textarea label="Description" wire:model="description" placeholder="Your story ..." hint="Max 1000 chars" rows="5" inline />

                    <x-choices-offline label="Status" wire:model="status_id" :options="$statuses" single searchable></x-choices-offline>
                    <x-choices-offline label="Priority" wire:model="priority_id" :options="$priorities" single searchable></x-choices-offline>

                    <x-choices-offline label="Categories" wire:model="category_id" :options="$categories" single searchable>
                        <x-slot:append>
                            <livewire:categories.create label="" class="rounded-l-none" />
                        </x-slot:append>
                    </x-choices-offline>
                    <x-tags label="Tags" wire:model="tags" icon="o-home" hint="Hit enter to create a new tag" />
                    @php
                    $config1 = ['altFormat' => 'd/m/Y'];
                    @endphp
                    <x-datepicker label="Start Date" wire:model="start_date" icon-right="o-calendar" :config="$config1" />
                    <x-datepicker label="Due Date" wire:model="due_date" icon-right="o-calendar" :config="$config1" />
                </div>
                

            </x-card>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/projects" />
            <x-button label="Save" icon="o-paper-airplane" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>
</div>


 
  
