<?php

namespace App\Traits;

trait QuantityTrait
{
    public function displayQuantities($student)
    {
        if ($student->billMethod && !empty($student->billMethod->quantity)) {
            return $student->billMethod->quantity;
        } else {
            return collect();
        }
    }
}