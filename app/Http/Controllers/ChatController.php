<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\UserChat;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class ChatController extends Controller
{
    public function index()
    {
        return view("chat.index", ["currentChat" => auth()->user()->userChats->first()]);
    }

    public function create(Request $request)
    {
        // TODO: check if user/s is not blocked
        $request->validate([
            "users" => "required"
        ]);


        $chat = null;
        $users = [];
        foreach ($request->users as $userId) {
            $users[] = User::find($userId);
        }

        if (count($users) == 2) {
            $chat = Chat::create([
                "name" => $users[0]["nickname"] . "," . $users[1]["nickname"],
                "is_private" => true,
            ]);
        } else {
            // TODO: group chat
        }

        foreach ($users as $user) {
            $userChat = UserChat::create([
                "user_id" => $user->id,
                "chat_id" => $chat->id,
                "name" => str_replace(",", "", str_replace($user->nickname, "", $chat->name)),
                "picture" => count($users) == 2 ? ($user->id == $users[0]["id"] ? $users[1]["profile_picture"] : $users[0]["profile_picture"]) : "img/chat/default_chat_picture.svg"
            ]);
        }

        return redirect(route("chat.index"));
    }

    public function lastMessage(int $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        $lastMessage = $chat->lastMessage;
        if (UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chatId)->exists()) {
            return response()->json([
                "lastMessage" => [
                    "nickname" => $lastMessage->user->nickname,
                    "content" => $lastMessage->content
                ]
            ]);
        }

        abort(401);
    }
}
