<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'year',
        'quantity',
        'payment_type',
    ];

    public function quantities()
    {
        return $this->hasMany(BillMethodQuantity::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'billmethod_id');
    }
}
