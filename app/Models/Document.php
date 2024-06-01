<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
  
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'tags', 
        'category_id', 'department_id', 'user_id'
    ];

    protected $casts = [
        'tags' => 'array',
       
    ];
    protected static function booted()
    {
        static::creating(function ($task) {
            $task->user_id = Auth::check() ? Auth::user()->id : 1;
            $task->department_id = Auth::check() ? Auth::user()->department_id : 1;
        });
    }

   

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function file(): HasMany
    {
        return $this->hasMany(File::class, 'model_id')->where('model_type', Document::class);

    }
    public function history(): HasMany
    {
        return $this->hasMany(History::class, 'model_id')->where('model_type', Document::class);

    }
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'documents_likes', 'document_id', 'user_id');
    }
}