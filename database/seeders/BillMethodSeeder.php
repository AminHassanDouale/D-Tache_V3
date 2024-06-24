<?php

namespace Database\Seeders;

use App\Models\BillMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (BillMethod::count()) {
            return;
        }

        BillMethod::insert([
            [
                'name' => 'mensuel',
                'amount' => '35000',
                'year' => '2024',
                'quantity' => '10'

            ],
            [
               'name' => 'bimensuel',
               'amount' => '70000',
               'year' => '2024',
               'quantity'=> '5',
            ],
            [
                'name' => 'trimesterial',
                'amount' => '105000',
                'year' => '2024',
                'quantity' => '3',
            ],
            [
                'name' => 'semesterial',
                'amount' => '175000',
                'year' => '2024',
                'quantity' => '2',
            

            ],
            [
                'name' => 'anuuel',
                'amount' => '350000',
                'year' => '2024',
                'quantity' => '1',
            

            ]
        ]);
    
    }
}
