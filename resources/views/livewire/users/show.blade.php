<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Department;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public User $user;
    public $roles;



    public function mount(User $user): void
    {
        $this->user = $user;
        $this->roles = Role::all();
    $this->user->load(['department']);
    }

   

   // public function documents(): Collection
   // {
   //     return Document::query() 
   //     ->where('user_id', $this->user->id)
   //     ->latest('id')
   //     ->take(10)
   //     ->get();         
   // }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Document'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'hidden lg:table-cell'],
            ['key' => 'created_at', 'label' => 'Date'],

        ];
    }

    public function with(): array
    {
        return [
           // 'documents' => $this->documents(),
            'headers' => $this->headers(),
            'Roles' => Role::all(),

        ];
    }
}; ?>

<div>
    <x-header :title="$user->name" separator>
        <x-slot:actions>
            <x-button label="Edit" link="/users/{{ $user->id }}/edit" icon="o-pencil" class="btn-primary" responsive />
        </x-slot:actions>
    </x-header>

    <div class="grid gap-8 lg:grid-cols-2">
        {{-- INFO --}}
        <x-card title="Info" separator shadow>
            <x-avatar :image="$user->avatar" class="!w-20">
                <x-slot:title class="pl-2">
                    {{ $user->name }}
                </x-slot:title>
                <x-slot:subtitle class="flex flex-col gap-2 p-2 pl-2">
                    <x-icon name="o-envelope" :label="$user->email" />
                    <x-icon name="o-map-pin" :label="$user->department->name" />
                </x-slot:subtitle>
            </x-avatar>
        </x-card>

        {{-- FAVORITES --}}
        <x-card title="detail" separator shadow>
            
            <x-slot:subtitle class="flex flex-col gap-2 p-2 pl-2">

            <x-icon name="o-device-phone-mobile" :label="$user->number" />
            <x-icon name="o-phone" :label="$user->fix" />
            </x-slot:subtitle>
        </x-card>
    </div>

    {{-- RECENT ORDERS 
    <x-card title="Document" separator shadow class="mt-8">
      
    </x-card> --}}


    <div class="w-full py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-2 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                
                <div class="p-2 mt-6 bg-slate-100">
                    <h2 class="text-2xl font-semibold">Roles</h2>
                    <div class="flex p-2 mt-4 space-x-2">
                        @if ($user->roles)
                            @foreach ($user->roles as $user_role)
                                <form class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-700" method="POST"
                                    action="{{ route('users.roles.remove', [$user->id, $user_role->id]) }}"
                                    onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">{{ $user_role->name }}</button>
                                </form>
                            @endforeach
                        @endif
                    </div>
                    <div class="max-w-xl mt-6">
                        <form method="POST" action="{{ route('users.roles', $user->id) }}">
                            @csrf
                            <div class="sm:col-span-6">
                                <label for="role" class="block text-sm font-medium text-gray-700">Roles</label>
                                <select id="role" name="role" autocomplete="role-name"
                                    class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role')
                                <span class="text-sm text-red-400">{{ $message }}</span>
                            @enderror
                    </div>
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 rounded-md hover:bg-green-700">Assign</button>
                    
                    </form>
                </div>
            </div>

        </div>
    </div>


    
</div>
