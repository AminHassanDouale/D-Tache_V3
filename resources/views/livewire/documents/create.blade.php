<?php

use App\Models\Brand;
use App\Models\Document;
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
    public int $category_id = 0;

   


    public bool $show = false;

    public string $label = 'Create';

    public function save(): void
    { 
        $this->validate([
        'name' => 'required',
        'description' => 'required',
        'category_id' => 'required',
    ]);

    $document = Document::create([
        'name' => $this->name,
        'description' => $this->description,
        'category_id' => $this->category_id, 
    ]);
   

    $this->show = false;
    $this->resetExcept('label', 'class');
    $this->dispatch('document-saved', id: $document->id);
    $this->success('Document created.');
    }

}; ?>

<div>
    <x-button :label="$label" @click="$wire.show = true" icon="o-plus" class="btn-primary {{ $class }}" responsive />

    {{-- This component can be used inside another forms. So we teleport it to body to avoid nested form submission conflict --}}
    <template x-teleport="body">
        <x-modal wire:model="show" title="Create Document">
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
                   $categories = App\Models\Category::take(10)->get();
                   @endphp
                    <x-choices-offline label="Category" wire:model="category_id" :options="$categories" single searchable></x-choices-offline>
                    


                <x-slot:actions>
                    <x-button label="Cancel" @click="$wire.show = false" />
                    <x-button label="Save" icon="o-paper-airplane" class="btn-primary" type="submit" />
                </x-slot:actions>
            </x-form>
        </x-modal>
    </template>
</div>
