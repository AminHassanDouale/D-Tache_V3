<?php

use Livewire\Volt\Component;

new class extends Component {
    
    public function search()
    {

    }
}; ?>

<div>
    <x-header title="Actor Search" separator progress-indicator />

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <x-form wire:submit="search">

                <x-input label="ActorID" name="actor_id" wire:model="actor_id"  icon="o-user"  />
                <x-slot:actions>
                    <x-button label="Search" icon="o-paper-airplane" class="btn-primary" type="submit" />
                </x-slot:actions>
            </x-form>
        </div>
        <div>
            <img src="/images/edit-form.png" width="300" class="mx-auto" />
        </div>
    </div>
</div>
