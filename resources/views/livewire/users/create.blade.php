<?php

use App\Actions\DeleteCustomerAction;
use App\Models\Country;
use App\Models\User;
use App\Models\Department;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithFileUploads;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required')]
    public ?int $department_id = null;

    #[Rule('nullable|image|max:1024')]
    public $avatar_file;

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create
        $data['password'] = Hash::make('password');
        $data['avatar'] = '/images/empty-user.jpg';


        if ($this->avatar_file) {
            $url = $this->avatar_file->store('users', 'public');
            $user->update(['avatar' => "/storage/$url"]);
        }

        $this->success('Customer created with success.', redirectTo: '/users');
    }

    public function with(): array
    {
        return [
            'departments' => Department::all(),
        ];
    }
}; ?>

<div>
    <x-header title="Nouveau Users" separator progress-indicator />

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <form method="post" action="{{ route('users.store') }}">
                @csrf
                
                <x-input label="Name" name="name" wire:model="name" />
                <x-input label="Email" name="email" wire:model="email" />
                <x-input label="Number" name="number" wire:model="number" />
                <x-select label="Department" name="department_id" wire:model="department_id" :options="$departments" placeholder="---" />
                <x-input label="Password" name="password"  type="password" icon="o-key"  />
                <x-input label="Confirmation Password" name="password_confirmation"  type="password" icon="o-key"  />

                    <x-button label="Cancel" link="/users" />
                    <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary"> Submit</x-button>
            </form>
        </div>
        <div>
            <img src="/images/edit-form.png" width="300" class="mx-auto" />
        </div>
    </div>
</div>
