<?php

use App\Actions\DeleteCustomerAction;
use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithFileUploads;

    public User $user;
    

    #[Rule('required')]
    public string $name = '';
    #[Rule('required')]
    public string $fix = '';

    #[Rule('required|email')]
    public string $email = '';
     #[Rule('required|numeric')]

    public string $number = '';

    

    #[Rule('required|numeric')]
    public ?int $department_id = null;

    #[Rule('nullable|image|max:1024')]
    public $avatar_file;

    public function mount(): void
    {
        $this->fill($this->user);
    }

    public function delete(): void
    {
        $action = new DeleteCustomerAction($this->user);
        $action->execute();

        $this->success('Deleted', redirectTo: '/users');
    }
    public function updatePassword(): void
    {
        // Validate password and confirmation_password
        $this->validate([
            'password' => 'required|min:6',
            'confirmation_password' => 'required|same:password',
        ]);

        // Update user's password
        $this->user->update(['password' => bcrypt($this->password)]);

        $this->success('Password updated successfully.');
    }

    public function update(): void
    {
        // Validate
        $data = $this->validate();

        // Update
        $this->user->update($data);

        $this->success('Customer updated with success.', redirectTo: '/users');
    }
    

    public function with(): array
    {
        return [
            'departments' => Department::all(),
        ];
    }
}; ?>

<div>
    <x-header :title="$user->name" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Delete" icon="o-trash" wire:click="delete" class="btn-error" wire:confirm="Are you sure?" spinner responsive />
        </x-slot:actions>
    </x-header>

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-input label="Name" wire:model="name" name="name" />
                <x-input label="Email" wire:model="email" name="email"  />
                <x-input label="number" wire:model="number" name="number" />
                <x-input label="fix" wire:model="fix" name="fix" />
                <x-select label="Department" wire:model="department_id" :options="$departments" placeholder="---" name="department_id" />

               
                    <x-button label="Cancel" link="/users" />
                    <x-button label="Update" icon="o-paper-airplane" spinner="update" type="submit" class="btn-primary" />
                
            </form>

        </div>
        
        <div>
            <img src="/images/edit-form.png" width="300" class="mx-auto" />
        </div>

        <x-card>
            <x-form wire:submit.prevent='updatePassword'>
                <x-input label="New Password" type="password" wire:model="password" name="password" />
                <x-input label="Confirm New Password" type="password" wire:model="confirmation_password" name="confirmation_password" />
                <x-button label="Update Password"  spinner="updatePassword" type="submit" class="btn-primary" />
            </x-form>
        </x-card>
    </div>
</div>
