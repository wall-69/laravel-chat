<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "chat_id", "content",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function toBroadcastArray()
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "chat_id" => $this->chat_id,
            "content" => $this->content,
            "created_at" => $this->created_at,
            "user" => [
                "id" => $this->user->id,
                "nickname" => $this->user->nickname,
                "profile_picture" => $this->user->profile_picture,
            ],
        ];
    }
}
