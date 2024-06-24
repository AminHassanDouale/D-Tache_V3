<?php

namespace Database\Factories;

use App\Models\BillMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillMethod>
 */
class BillMethodFactory extends Factory
{
    protected $model = BillMethod::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['mensuel', 'bimensuel', 'trimesterial', 'semesterial', 'annuel']),
            'amount' => $this->faker->randomFloat(2, 10000, 50000),
            'year' => $this->faker->year,
            'quantity' => $this->faker->numberBetween(1, 10),
            'payment_type' => $this->faker->randomElement(['cash', 'online', 'cheque', 'D-Money', 'SABA_Pay', 'WAAFI', 'CAC_Pay']),
        ];
    }
}
