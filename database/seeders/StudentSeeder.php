<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\BillMethod;
use App\Models\BillMethodQuantity;

class StudentSeeder extends Seeder
{
    public function run()
    {
        Student::factory()->count(100)->create();

    }
}
