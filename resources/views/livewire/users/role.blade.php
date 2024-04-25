<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


new
#[Layout('components.app')]
#[Title('roles')]


class extends Component {
    public $roles;

public function mount() {
    $this->roles = Role::all();
}
public function with(): array
    {
        return [

            'Roles' => Role::all(),

        ];
    }

}; ?>
    <div class="w-full py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-2 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex p-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 bg-green-700 rounded-md hover:bg-green-500 text-slate-100">Users Index</a>
                </div>
                <div class="flex flex-col p-2 bg-slate-100">
                    <div>User Name: {{ $user->name }}</div>
                    <div>User Email: {{ $user->email }}</div>
                </div>
                <div class="p-2 mt-6 bg-slate-100">
                    <h2 class="text-2xl font-semibold">Roles</h2>
                    <div class="flex p-2 mt-4 space-x-2">
                        @if ($user->roles)
                            @foreach ($user->roles as $user_role)
                                <form class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-700" method="POST"
                                    action="{{ route('admin.users.roles.remove', [$user->id, $user_role->id]) }}"
                                    onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">{{ $user_role->name }}</button>
                                </form>
                            @endforeach
                        @endif
                    </div>
                    <div class="max-w-xl mt-6">
                        <form method="POST" action="{{ route('admin.users.roles', $user->id) }}">
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
                    <div class="pt-5 sm:col-span-6">
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 rounded-md hover:bg-green-700">Assign</button>
                    </div>
                    </form>
                </div>
                <div class="p-2 mt-6 bg-slate-100">
                    <h2 class="text-2xl font-semibold">Permissions</h2>
                    <div class="flex p-2 mt-4 space-x-2">
                        @if ($user->permissions)
                            @foreach ($user->permissions as $user_permission)
                                <form class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-700" method="POST"
                                    action="{{ route('admin.users.permissions.revoke', [$user->id, $user_permission->id]) }}"
                                    onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">{{ $user_permission->name }}</button>
                                </form>
                            @endforeach
                        @endif
                    </div>
                    <div class="max-w-xl mt-6">
                        <form method="POST" action="{{ route('admin.users.permissions', $user->id) }}">
                            @csrf
                            <div class="sm:col-span-6">
                                <label for="permission"
                                    class="block text-sm font-medium text-gray-700">Permission</label>
                                <select id="permission" name="permission" autocomplete="permission-name"
                                    class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach ($permissions as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('name')
                                <span class="text-sm text-red-400">{{ $message }}</span>
                            @enderror
                    </div>
                    <div class="pt-5 sm:col-span-6">
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 rounded-md hover:bg-green-700">Assign</button>
                    </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
    </div>
</x-admin-layout>