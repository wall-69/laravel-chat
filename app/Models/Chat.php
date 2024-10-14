<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "is_private", "last_message"
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

    public function chatAdmin()
    {
        return $this->hasOne(ChatAdmin::class);
    }
}
