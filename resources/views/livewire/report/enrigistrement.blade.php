<?php

use App\Actions\DeleteCustomerAction;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Models\Department;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Mary\Traits\WithMediaSync;
use App\Models\Enrigistrement;



new class extends Component {
    use Toast, WithFileUploads, WithMediaSync;

    public string $full_name = '';
    public string $address = '';
    public string $dob = '';
    public ?int $department_id = null;
    public string $msisdn = '';
    public ?string $gender = null;
    public string $personne_contact = '';
    public string $personne_contact_tel = '';
    public $attachment_photo;
    public $attachment_identite;
    public $attachment_formulaire;

    // Validation rules for inputs
    protected $rules = [
        'full_name' => 'required|string',
        'address' => 'required|string',
        'dob' => 'required|date',
        'msisdn' => 'required|string',
        'gender' => 'required|string|in:male,female',
        'personne_contact' => 'required|string',
        'personne_contact_tel' => 'required|string',
        'attachment_photo' => 'nullable|image|max:1024',
        'attachment_identite' => 'nullable|image|max:1024',
        'attachment_formulaire' => 'nullable|image|max:1024',
    ];
    public function save(): void
    {
        $this->validate();
        $enrigistrement = new Enrigistrement();
        $enrigistrement->full_name = $this->full_name;
        $enrigistrement->address = $this->address;
        $enrigistrement->dob = $this->dob;
        $enrigistrement->msisdn = $this->msisdn;
        $enrigistrement->gender = $this->gender;
        $enrigistrement->personne_contact = $this->personne_contact;
        $enrigistrement->personne_contact_tel = $this->personne_contact_tel;
        
        if ($this->attachment_photo) {
            $photoPath = $this->attachment_photo->store('photos', 'public');
            $enrigistrement->attachment_photo = $photoPath;
        }
        if ($this->attachment_identite) {
            $identitePath = $this->attachment_identite->store('identites', 'public');
            $enrigistrement->attachment_identite = $identitePath;
        }
        if ($this->attachment_formulaire) {
            $formulairePath = $this->attachment_formulaire->store('formulaires', 'public');
            $enrigistrement->attachment_formulaire = $formulairePath;
        }

        // Save the enrigistrement
        $enrigistrement->save();

        
        $this->success('Enrigistrement created with success.', redirectTo: '/enrigistrements');
    }


    public function with(): array
    {
        return [
            'departments' => Department::all(),
        ];
    }
}; ?>

<div>
    <x-header title="Nouveau RDS" separator progress-indicator />

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <x-form wire.prevent="save" enctype="multipart/form-data">
                <x-input label="Nom" name="full_name" wire:model="full_name" />
                <x-input label="address" name="address" wire:model="Address" />
                @php
                $config1 = ['altFormat' => 'd/m/Y'];
                $config2 = ['mode' => 'range'];
                @endphp
                <x-datepicker label="date de naissance" wire:model="dob" name="dob" icon-right="o-calendar" :config="$config1" />
                <x-select label="Department" name="department_id" wire:model="department_id" :options="$departments" placeholder="---" />
                <x-input label="msisdn" name="msisdn"  type="password" icon="o-phone"  />
               <input type="radio" value="male" name="gender" > Male
              <br>
               <input type="radio" value="female" name="gender" > Female
               <x-input label="Personne Contact" name="personne_contact" wire:model="personne_contact" />
               <x-input label="Personne Contact msisdn" name="personne_contact_tel" wire:model="personne_contact_tel" />
                    <x-file wire:model="attachment_photo" label="photo" name="attachment_photo" />
                    <x-file wire:model="attachment_identite" label="identite" name="attachment_identite" />
                    <x-file wire:model="attachment_formulaire" label="formulaire" name="attachment_formulaire" />
               <br>
                    <x-button label="Cancel" link="/users" />
                    <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary"> Submit</x-button>
            </x-form>
        </div>
        <div>
            <img src="/images/edit-form.png" width="300" class="mx-auto" />
        </div>
    </div>
</div>
