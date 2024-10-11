<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use App\Models\UserChat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Shows the chat index page.
     */
    public function index()
    {
        // Gets the user's UserChats alongside the chat and chat's last message and sorts them based on the last_message column
        $chatOrder = auth()->user()->userChats()
            ->with([
                "chat",
                "chat.lastMessage.user:id,nickname",
            ])
            ->get()
            ->sortByDesc(function ($userChat) {
                return $userChat->chat->last_message === null ? PHP_INT_MAX : strtotime($userChat->chat->last_message);
            })
            ->values()
            ->toArray();

        // If there are more than 1 UserChats, update the first chat's last_read column to current time
        if (count($chatOrder) >= 1) {
            $firstChat = UserChat::find($chatOrder[0]["id"]);
            $firstChat->update(["last_read" => now()->toISOString()]);
            $chatOrder[0]["last_read"] = now()->toISOString();
        }

        return view("chat.index", ["chatOrder" => $chatOrder]);
    }

    /**
     * Stores the chat with specified users and also creates UserChats.
     */
    public function store(Request $request)
    {
        // Validate
        $request->validate([
            "users" => "required"
        ]);

        $chat = null;
        $users = []; // Array for the User models
        foreach ($request->users as $userId) {
            $users[] = User::find($userId);
        }

        // If there are only 2 users, create private chat
        if (count($users) == 2) {
            // Check, if there is a UserBlock between these two users
            if ($users[1]->userBlocks->contains("blocked_user_id", $users[0]->id)) {
                return back();
            }

            // Check, if the Chat model already exists
            $chat = Chat::whereIn("name", [
                $users[0]["nickname"] . "," . $users[1]["nickname"],
                $users[1]["nickname"] . "," . $users[0]["nickname"]
            ])->first();

            // Create the Chat, if one doesnt exist
            if (!$chat) {
                $chat = Chat::create([
                    "name" => $users[0]["nickname"] . "," . $users[1]["nickname"],
                    "is_private" => true,
                ]);
            } else {
                // Update the last_message column to current time, to make it appear at the top of chat order
                $chat->update(["last_message" => now()->toISOString()]);
            }
        } else {
            // TODO: group chat
        }

        // Create the UserChat for all users in the array
        foreach ($users as $user) {
            // If this user already has this UserChat, we skip him
            if (UserChat::where("user_id", $user->id)->where("chat_id", $chat->id)->exists()) {
                continue;
            }

            // Create the UserChat
            $userChat = UserChat::create([
                "user_id" => $user->id,
                "chat_id" => $chat->id,
                "name" => str_replace(",", "", str_replace($user->nickname, "", $chat->name)),
                "picture" => count($users) == 2 ? ($user->id == $users[0]["id"] ? $users[1]["profile_picture"] : $users[0]["profile_picture"]) : "img/chat/default_chat_picture.svg"
            ]);
        }

        return redirect(route("chat.index"));
    }
}
