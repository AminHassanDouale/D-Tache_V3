<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name','number','email','department_id','avatar','password','fix','status'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

   

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => ucfirst(str($this->name)->explode(' ')->first())
        );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class,'assigned_id');
    }
    public function members()
    {
        return $this->hasMany(Member::class,'user_id');
    }
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'documents_likes', 'user_id', 'document_id');
    }

}
