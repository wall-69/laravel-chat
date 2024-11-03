<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "chat_id", "last_read"
    ];

    protected $casts = [
        "last_read" => "datetime"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function isDM()
    {
        return $this->chat->isDM();
    }

    public function isChannel()
    {
        return $this->chat->isChannel();
    }
}
