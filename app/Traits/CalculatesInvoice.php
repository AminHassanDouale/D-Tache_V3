<?php

namespace App\Traits;

use App\Models\BillMethodQuantity;

trait CalculatesInvoice
{
    public function calculateTotalAmount($bill_method_quantity_id, $payment_type, $discount)
    {
        $amount = BillMethodQuantity::findOrFail($bill_method_quantity_id)->amount;

        if ($payment_type === 'partially') {
            $amount -= $discount;
        }

        return $amount;
    }
}
