<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;


class Category extends Model
{
    use HasFactory;

   // protected $guarded = ['id'];
     protected $fillable = ['id','name', 'deprtment_id', 'user_id'];

    protected static function booted()
    {
        static::creating(function ($project) {
            $project->user_id       = Auth::check() ? Auth::id() : 1 ;
            $project->department_id = Auth::check() ? Auth::user()->department_id : 1 ;
           
        });
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, Product::class);
    }

    protected function dateHuman(): Attribute
    {
        return Attribute::make(
            get: fn(?Carbon $value) => $this->created_at->toFormattedDateString()
        );
    }
}
