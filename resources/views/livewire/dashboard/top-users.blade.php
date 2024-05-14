<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public string $period = '-30 days';

    public function topCustomers(): Collection
    {
        $departmentId = Auth::user()->department_id;

        return User::where('department_id', $departmentId)
            ->with([
                'tasks' => function ($query) {
                    $query->where('created_at', '>=', Carbon::parse($this->period)->startOfDay());
                },
            ])
            ->get();
    }

    public function with(): array
    {
        return [
            'usersWithTasks' => $this->topCustomers(),
        ];
    }
}; ?>
<div>
    <x-card title="Equipe" separator shadow>


        @foreach ($usersWithTasks as $user)
            <x-list-item :item="$user" sub-value="country.name" link="/users/{{ $user->id }}" no-separator>
                <h2>{{ $user->name }}</h2>
                <x-slot:actions>
                    <x-badge :value="$user->tasks->count()" class="font-bold" />
                </x-slot:actions>
            </x-list-item>
        @endforeach
    </x-card>
</div>
