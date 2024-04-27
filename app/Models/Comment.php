<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['name','comment','date','user_id','department_id','model_id'];


    public function model()
    {
        return $this->morphTo();
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
}
