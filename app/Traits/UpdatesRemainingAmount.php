<?php

namespace App\Traits;

use App\Models\BillMethodQuantity;

trait UpdatesRemainingAmount
{
    public function updateRemainingAmount($bill_method_quantity_id, $paidAmount)
    {
        $billMethodQuantity = BillMethodQuantity::findOrFail($bill_method_quantity_id);
        $billMethodQuantity->remaining -= $paidAmount;
        $billMethodQuantity->save();
    }
}
