<?php

namespace Database\Factories;

use App\Models\BillMethodQuantity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillMethodQuantity>
 */
class BillMethodQuantityFactory extends Factory
{
    protected $model = BillMethodQuantity::class;

    public function definition()
    {
        return [
            'quantity' => 1, // Default quantity value, will be overridden in the Student factory
            'remaining' => 0, // Default remaining value, will be overridden in the Student factory
            'amount' => 0, // Default amount value, will be overridden in the Student factory
        ];
    }
}
