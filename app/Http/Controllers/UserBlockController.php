<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserChat;
use Illuminate\Http\Request;

class UserBlockController extends Controller
{
    /**
     * Stores the UserBlock to database.
     */
    public function store(Request $request)
    {
        // Validate
        $request->validate([
            "blocked_user" => "required"
        ]);

        // Get the blocking user id and blocked user id
        $userId = auth()->user()->id;
        $blockedUserId = $request->blocked_user;

        // Check, if there is already existing UserBlock
        if (UserBlock::where("user_id", $userId)->where("blocked_user_id", $blockedUserId)->exists()) {
            abort(400, "User is already blocked.");
        } else {
            // Create the UserBlock
            UserBlock::create([
                "user_id" => $userId,
                "blocked_user_id" => $blockedUserId
            ]);

            // TODO:
            // Find the Chat for this DM
            // $chat = Chat::whereType("dm")->whereHas("userChats", function ($query) {
            //     $query->where("user_id", auth()->user()->id);
            // })->get();
            // dd($chat);
            // Remove the UserChat of the user that is blocking, if one exists
            // $userChat = UserChat::where("user_id", $userId);
            // if ($userChat) {
            //     $userChat->delete();
            // }

            return back();
        }
    }

    /**
     * Deletes the UserBlock.
     */
    public function destroy(UserBlock $userBlock)
    {
        $userBlock->delete();

        return back();
    }
}
