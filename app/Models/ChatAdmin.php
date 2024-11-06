<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "chat_id"
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
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
