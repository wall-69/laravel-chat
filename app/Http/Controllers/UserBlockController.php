<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserChat;
use Illuminate\Http\Request;

class UserBlockController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "blocked_user" => "required"
        ]);

        $userId = auth()->user()->id;
        $blockedUserId = $request->blocked_user;

        if (UserBlock::where("user_id", $userId)->where("blocked_user_id", $blockedUserId)->exists()) {
            abort(400, "User is already blocked.");
        } else {
            UserBlock::create([
                "user_id" => $userId,
                "blocked_user_id" => $blockedUserId
            ]);

            $userChat = UserChat::where("user_id", $userId)->where("name", User::find($blockedUserId)->nickname);
            if ($userChat) {
                $userChat->delete();
            }

            return back();
        }
    }

    public function destroy(UserBlock $userBlock)
    {
        $userBlock->delete();

        return back();
    }
}
