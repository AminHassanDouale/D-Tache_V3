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
                'name' => 'A faire',
                'description' => '1er phase',
                'color' => 'bg-neutral/20',
                'icon' => 'o-shopping-bag'
            ],
            [
                'name' => 'en cours de traitement',
                'description' => '2eme phase',
                'color' => 'bg-purple-500/20',
                'icon' => 'o-map-pin'
            ],
            [
                'name' => 'Verification',
                'description' => '3eme phase',
                'color' => 'bg-info/20',
                'icon' => 'o-credit-card'
            ],
            [
                'name' => 'Confirmation',
                'description' => '4eme phase',
                'color' => 'bg-warning/20',
                'icon' => 'o-paper-airplane'
            ],
            [
                'name' => 'Fait',
                'description' => '5eme phase',
                'color' => 'bg-success/20',
                'icon' => 'o-gift'
            ]
        ]);
    }
}
