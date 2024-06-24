<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillMethodQuantity extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_method_id',
        'student_id',
        'quantity',
        'remaining',
        'amount',
        'status_id',
    ];

    public function billMethod()
    {
        return $this->belongsTo(BillMethod::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'bill_method_quantities_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
