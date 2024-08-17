<?php

use App\Models\User;
use App\Models\UserChat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

Broadcast::channel("chats.{chatId}", function (User $user, int $chatId) {
    return UserChat::where("user_id", $user->id)->where("chat_id", $chatId)->exists();
});
