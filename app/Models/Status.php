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


}
