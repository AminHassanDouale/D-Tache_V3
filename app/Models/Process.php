<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Process extends Model
{
    use HasFactory;

    // Assuming the primary key is 'id' and it's auto-incrementing
    protected $primaryKey = 'id';

    // Assuming the table name is 'tasks'
    protected $table = 'processes';

    // Define the fillable attributes to protect against mass assignment vulnerabilities
    protected $fillable = [
        'name', 'description', 'assigned_id', 'tags', 'signature', 'start_date', 'due_date',
        'category_id', 'status_id', 'project_id', 'department_id', 'user_id', 'priority_id'
    ];

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
            $project->status_id = '1';
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
    public function file(): HasMany
    {
        return $this->hasMany(File::class, 'model_id')->where('model_type', Task::class);

    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'model');
    }
   public function histories()
   {
       return $this->morphMany(History::class, 'model');
   }
   public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function tasks()
    {
        return Task::where('project_id', $this->project->id)->get();
    }
}
