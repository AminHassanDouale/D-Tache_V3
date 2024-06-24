<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // In your Status model
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }


}
