<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class Enrigistrement extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'adresse',
        'dob',
        'msisdn',
        'gender',
        'numero_piece_identite',
        'personne_contact',
        'personne_contact_tel',
        'attachment_photo',
        'attachment_identite',
        'attachment_formulaire',
        'user_id',
        'department_id',
    ];
    protected static function booted()
    {
        static::creating(function ($project) {
            $project->user_id = Auth::user()->id;
            $project->department_id = Auth::user()->department_id;
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    protected $casts = [
        'library' => AsCollection::class,
    ];
}
