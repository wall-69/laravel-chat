<?php

namespace App\Http\Controllers;

use App\Models\UserChat;
use Illuminate\Http\Request;

class UserChatController extends Controller
{
    public function updateLastRead(int $userChatId)
    {
        $userChat = UserChat::findOrFail($userChatId);

        $userChat->update(["last_read" => now()->toISOString()]);

        return response()->json([
            "message" => "Last read updated successfully!"
        ]);
    }
}
