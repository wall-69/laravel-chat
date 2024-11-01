<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "type", "picture", "is_private", "last_message"
    ];

    protected $casts = [
        "last_message" => "datetime"
    ];

    public function userChats()
    {
        return $this->hasMany(UserChat::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function admin()
    {
        return $this->hasOne(ChatAdmin::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "user_chats");
    }

    public function bans()
    {
        return $this->hasMany(UserChatBan::class);
    }

    public function isDM()
    {
        return $this->type == "dm";
    }

    public function isChannel()
    {
        return $this->type == "channel";
    }
}
