<?php

namespace Database\Seeders;

use App\Models\Engagment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EngagmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Engagment::count()) {
            return;
        }

        Engagment::insert([
            [
                'name' => 'trimister',
               'amount' => '30000',
               'year' => '2023',
            ],
            [
                'name' => 'semester',
                'amount' => '60000',
                'year' => '2024',

                
               
            ],
            [
                'name' => 'annee',
                'amount' => '120000',
                'year' => '2025',

            ]
        ]);
    
    }
}
