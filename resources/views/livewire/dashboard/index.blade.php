<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new class extends Component {
    #[Url]
    public string $period = '-100 days';

    // Available periods
    public function periods(): array
    {
        return [
            [
                'id' => '-7 days',
                'name' => 'Last 7 days',
            ],
            [
                'id' => '-15 days',
                'name' => 'Last 15 days',
            ],
            [
                'id' => '-30 days',
                'name' => 'Last 30 days',
            ],
            [
                'id' => '-40 days',
                'name' => 'Last 40 days',
            ],
            [
                'id' => '-50 days',
                'name' => 'Last 50 days',
            ],
            [
                'id' => '-60 days',
                'name' => 'Last 60 days',
            ],
            [
                'id' => '-70 days',
                'name' => 'Last 70 days',
            ],
            [
                'id' => '-80 days',
                'name' => 'Last 80 days',
            ],
            [
                'id' => '-90 days',
                'name' => 'Last 90 days',
            ],
            [
                'id' => '-100 days',
                'name' => 'Last 100 days',
            ],
        ];
    }

    public function with(): array
    {
        return [
            'periods' => $this->periods()
        ];
    }
} ?>

<div>
    <x-header title="Dashboard" separator progress-indicator>
        
        <x-slot:actions>
            <x-select :options="$periods" wire:model.live="period" icon="o-calendar" />
        </x-slot:actions>
    </x-header>

    {{--  STATISTICS   --}}
    <livewire:dashboard.stats :$period />

    <div class="grid gap-8 mt-8 lg:grid-cols-6">
        {{-- GROSS --}}
        <div class="col-span-6 lg:col-span-4">
            <livewire:dashboard.chart-gross :$period />
        </div>
       

        {{-- PER CATEGORY --}}
        <div class="col-span-6 lg:col-span-2">
            <livewire:dashboard.chart-category :$period />
        </div>
       
    </div>

    <div class="grid gap-8 mt-8 lg:grid-cols-4">
        {{-- TOP CUSTOMERS --}}
        <div class="col-span-2">
            <livewire:dashboard.top-customers :$period />
        </div>

        {{-- BEST SELLERS  --}}
        <div class="col-span-2">
            <livewire:dashboard.best-sellers :$period />
        </div>

    </div>

    {{-- LATEST ORDERS --}}
    <livewire:dashboard.oldest-orders :$period />
     
</div>
