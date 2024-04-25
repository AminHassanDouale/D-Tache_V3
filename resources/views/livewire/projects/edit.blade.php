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

new #[Layout('components.app')] class extends Component {

    use Toast, WithFileUploads, WithMediaSync;

    public $name, $description, $status_id, $category_id, $start_date, $due_date, $priority_id;
    public $tags = [];
    public Project $project;


    public function mount(Project $project): void
    {
        if (!$project) {
        // Handle missing project (e.g., display error message or redirect)
        abort(404, 'Project not found');
    }

    // Proceed with normal operations since the project exists
    $this->project = $project;

    // Fill other component properties with project data as needed
    $this->fill([
        'name' => $project->name,
        'description' => $project->description,
        'status_id' => $project->status_id,
        'category_id' => $project->category_id,
        'start_date' => $project->start_date,
        'due_date' => $project->due_date,
        'priority_id' => $project->priority_id,
        'tags' => $project->tags,
    ]);

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

    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }
    public function priorities(): Collection
    {
        return Priority::orderBy('name')->get();
    }

    public function updateproject(): void
{
    $validated = $this->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'priority_id' => 'required|exists:priorities,id',
        'status_id' => 'required|exists:statuses,id',
        'category_id' => 'required|exists:categories,id',
        'start_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:start_date',
        'tags' => 'array',
    ]);

    // Update the project with the validated data
    $this->project->update($validated);
dd($this);
    // Display a toast notification
    $this->toast(
        type: 'warning',
        title: 'Mise A jour!',
        description: null,                 // optional (text)
        position: 'toast-bottom toast-start',    // optional (daisyUI classes)
        icon: 'o-information-circle',       // Optional (any icon)
        css: 'alert-warning',                 // Optional (daisyUI classes)
        timeout: 3000,                      // optional (ms)
        redirectTo: null                    // optional (uri)
    );
}

    public function with(Project $project): array
    {
        $this->project = $project;

        return [
            'statuses' => $this->statuses(),
            'categories' => $this->categories(),
            'priorities' => $this->priorities(),
            
        ];
    }
}; ?>

<div>
    <x-header :title="$project->name" separator>
        <x-slot:actions>
            <x-button label="Delete" icon="o-trash" wire:click="delete" class="btn-error" wire:confirm="Are you sure?" spinner responsive />
        </x-slot:actions>
    </x-header>

    <x-form wire:submit.prevent="updateproject">
        @csrf <!-- Add CSRF token -->

        <div class="grid gap-8 lg:grid-cols-2">
            {{-- DETAILS --}}
            <x-card title="Details" separator>
                <div class="grid gap-5 lg:px-3" wire:key="details">
                    <x-input label="Name" wire:model="name" />
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror

                    <x-textarea
                        label="Description"
                        wire:model="description"
                        placeholder=" ..."
                        hint="Max 1000 chars"
                        rows="5"
                        inline
                    />
                    @error('description') <span class="text-red-500">{{ $message }}</span> @enderror

                    <x-choices-offline label="Status" wire:model="status_id" :options="$statuses" single searchable>
                        <x-slot:append>
                            <livewire:brands.create label="" class="rounded-l-none" />
                        </x-slot:append>
                    </x-choices-offline>
                    @error('status_id') <span class="text-red-500">{{ $message }}</span> @enderror

                    <x-choices-offline label="Categories" wire:model="category_id" :options="$categories" single searchable>
                        <x-slot:append>
                            <livewire:categories.create label="" class="rounded-l-none" />
                        </x-slot:append>
                    </x-choices-offline>
                    @error('category_id') <span class="text-red-500">{{ $message }}</span> @enderror

                    <x-tags label="Tags" wire:model="tags" icon="o-home" hint="Hit enter to create a new tag" />
                    @php
                    $config1 = ['altFormat' => 'd/m/Y'];
                    @endphp
                    <x-datepicker label="Start Date" wire:model="start_date" icon-right="o-calendar" :config="$config1" />
                    <x-datepicker label="Due Date" wire:model="due_date" icon-right="o-calendar" :config="$config1" />
                </div>
            </x-card>

            <div class="grid content-start gap-8">
                {{-- COVER IMAGE --}}
                <x-card title="Files" separator>
                    <!-- Add file upload inputs and logic here if needed -->
                </x-card>

                {{-- MORE IMAGES --}}
                <x-card title="More images" separator>
                    <!-- Add more image upload inputs and logic here if needed -->
                </x-card>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/projects" />
            <x-button label="Save Changes" spinner="saveTask" type="submit" icon="o-paper-airplane" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
