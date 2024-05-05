<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new
#[Layout('components.layouts.empty')]
#[Title('Login')]


class extends Component {
    public $email;
    public $password;
    public function login()
{
    $credentials = $this->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        return redirect('/home');
    }

    // Handle failed login attempt
    $this->addError('email', 'These credentials do not match our records.');
}

}; ?>

<div class="mx-auto mt-20 md:w-96">
<alert />
    <x-form wire:submit="login">
        @csrf
        <x-input label="E-mail" name="email" wire:model="email" icon="o-envelope" inline />
<x-input label="Password" name="password" wire:model="password" type="password" icon="o-key" inline />

        <x-slot:actions>
            <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login" />
        </x-slot:actions>
    </x-form>
</div>

