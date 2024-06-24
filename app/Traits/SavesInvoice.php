<?php

namespace App\Traits;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

trait SavesInvoice
{
    public function saveInvoice($data)
    {
        Invoice::create($data);
    }
}
