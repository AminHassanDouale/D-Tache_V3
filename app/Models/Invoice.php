<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'bill_method_quantities_id',
        'student_name',
        'studentId',
        'subject',
        'invoiceId',
        'start_date',
        'due_date',
        'amount',
        'discount',
        'user_id',
        'status_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function billMethodQuantity()
    {
        return $this->belongsTo(BillMethodQuantity::class, 'bill_method_quantities_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
