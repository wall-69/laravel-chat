<?php

namespace App\Http\Controllers;

use App\Models\UserChat;
use Illuminate\Http\Request;

class UserChatController extends Controller
{
    // TODO: should be API?
    /**
     * Updates the last_read column in the UserChat to current time.
     */
    public function updateLastRead(UserChat $userChat)
    {
        // Update to current time
        $userChat->update(["last_read" => now()->toISOString()]);

        return response()->json([
            "message" => "Last read was updated successfully!"
        ]);
    }
}
