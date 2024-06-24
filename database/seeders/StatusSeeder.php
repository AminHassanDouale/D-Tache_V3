<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Status::count()) {
            return;
        }

        Status::insert([
          
           
            [
                'name' => 'not paid',
                'color' => 'bg-info/20',
                'icon' => 'o-credit-card'
            ],
            [
                'name' => 'partially paid',
                'color' => 'bg-warning/20',
                'icon' => 'o-paper-airplane'
            ],
            [
                'name' => 'paid',
                'color' => 'bg-success/20',
                'icon' => 'o-gift'
            ]
        ]);
    }
}
