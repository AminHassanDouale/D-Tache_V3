<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'pin_code',
        'city',
        'state',
        'phone',
        'email',
        'studentId',
        'status_id',
        'join_date',
        'birth_date',
        'total_amount',
        'billmethod_id',
    ];

    public function billMethod()
    {
        return $this->belongsTo(BillMethod::class, 'billmethod_id');
    }

    public function billMethodQuantities()
    {
        return $this->hasMany(BillMethodQuantity::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, BillMethodQuantity::class, 'student_id', 'bill_method_quantities_id');
    }
}
