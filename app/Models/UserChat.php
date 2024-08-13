<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "chat_id", "name", "picture", "joined_at"
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
