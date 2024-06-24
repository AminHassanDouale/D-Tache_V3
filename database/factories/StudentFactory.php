<?php

namespace Database\Factories;

use App\Models\BillMethod;
use App\Models\Engagment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'pin_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'studentId' => $this->faker->unique()->numberBetween(1000, 9999),
            'join_date' => $this->faker->date,
            'birth_date' => $this->faker->date,
            'total_amount' => 350000, 
            'billmethod_id' => $this->faker->numberBetween(1, 5),
            'status_id' => $this->faker->numberBetween(1, 3),

        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Student $student) {
            $billMethod = $student->billMethod;
            for ($i = 0; $i < $billMethod->quantity; $i++) {
                $student->billMethodQuantities()->create([
                    'bill_method_id' => $billMethod->id,
                    'student_id' => $student->id,
                    'quantity' => $i + 1,
                    'remaining' => $billMethod->amount - (($i + 1) * ($billMethod->amount / $billMethod->quantity)),
                    'amount' => $billMethod->amount,
                ]);
            }
        });
    }
    }

