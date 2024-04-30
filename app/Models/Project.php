<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class Project extends Model
{
    use HasFactory;


    protected $table = 'projects';


    protected $fillable = ['name', 'description', 'priority_id', 'status_id', 'category_id', 'start_date', 'due_date', 'tags','user_id', 'department_id'];


    protected $casts = [
        'tags' => 'array',
        'start_date' => 'date',
        'due_date' => 'date',
    ];
    protected static function booted()
    {
        static::creating(function ($project) {
            $project->user_id = Auth::user()->id;
            $project->department_id = Auth::user()->department_id;
        });
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function members()
{
    return $this->morphMany(Member::class, 'model');
}
public function owner()
{
    return $this->belongsTo(User::class, 'user_id'); 
}
}
