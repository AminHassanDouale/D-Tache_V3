<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Priority::count()) {
            return;
        }

        Priority::insert([
            [
                'name' => 'faible',
                'description' => '<img src="https://cdn.hugeicons.com/icons/flag-03-duotone-rounded.svg" alt="flag-03" width="32" height="32" />',
                'color' => 'bg-neutral/20',
                
            ],
            [
                'name' => 'normale',
                'description' => '<img src="https://cdn.hugeicons.com/icons/flag-03-duotone-rounded.svg" alt="flag-03" width="33" height="33" />',
                'color' => 'bg-purple-500/20',
            ],
            [
                'name' => 'important',
                'description' => '<img src="https://cdn.hugeicons.com/icons/flag-03-duotone-rounded.svg" alt="flag-03" width="34" height="34" />',
                'color' => 'bg-info/20',
            ],
            [
                'name' => 'urgent',
                'description' => '<img src="https://cdn.hugeicons.com/icons/flag-03-duotone-rounded.svg" alt="flag-03" width="35" height="34" />',
                'color' => 'bg-danger/20',
            ],
          
        ]);
    }
}
