<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // We are constraining the relationship to a single chat per Game
    public function chatroom(): HasOne
    {
        return $this->hasOne(ChatRoom::class);
    }
}
