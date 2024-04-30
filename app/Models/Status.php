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
public function getColorAttribute()
{
    // Example logic to determine the color class
    switch ($this->name) {
        case 'A faire':
            return 'bg-yellow-500';
        case 'en cours de traitement':
            return 'bg-yellow-500';
            case 'Verification':
            return 'bg-yellow-500';
            case 'Confirmation':
            return 'bg-yellow-500';
            case 'Terminer':
            return 'bg-yellow-500';
        case 'On Hold':
            return 'bg-red-500';
        default:
            return 'bg-gray-500';
    }
}

}
