<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatAdmin;
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
                "chat.users:id,nickname"
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
     * Shows the channels page.
     */
    public function channels()
    {
        $channels = Chat::whereIsPrivate(false)->get();

        return view("chat.channels", ["channels" => $channels]);
    }

    /**
     * Shows the chat(channel) create page.
     */
    public function create()
    {
        return view("chat.create");
    }

    /**
     * Stores the chat with specified users and also creates UserChats.
     */
    public function store(Request $request)
    {
        // Validate
        $request->validate([
            "users" => "required",
        ]);

        $chat = null;
        $chat_picture = null;
        $users = []; // Array for the User models
        foreach ($request->users as $userId) {
            $users[] = User::find($userId);
        }

        // Check, if the first user which is creating is actually the one authenticated
        if ($users[0]->id != auth()->user()->id) {
            abort(400);
        }

        // If there are only 1 users, create channel, else a private chat
        if (count($users) == 1) {
            $request->validate([
                "name" => "required|min:3",
                "is_private" => "required|bool",
                "chat_picture" => "required|image"
            ]);

            // Chat picture
            $filePath = $request->chat_picture->store("img/chat_pictures", "public");
            $chat_picture = "storage/" . $filePath;

            // Create the Chat
            $chat = Chat::create([
                "name" => $request->name,
                "is_private" => $request->is_private,
            ]);

            // Make the user a ChatAdmin
            ChatAdmin::create([
                "user_id" => $users[0]->id,
                "chat_id" => $chat->id
            ]);
        } else {
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
        }

        // Create the UserChat for all users in the array
        foreach ($users as $user) {
            // If this user already has this UserChat, we skip him
            if (UserChat::where("user_id", $user->id)->where("chat_id", $chat->id)->exists()) {
                continue;
            }

            // Create the UserChat
            UserChat::create([
                "user_id" => $user->id,
                "chat_id" => $chat->id,
                // TODO: cleanup or whatefver this fukcing sucks
                "name" => count($users) == 2 ? str_replace(",", "", str_replace($user->nickname, "", $chat->name)) : $chat->name,
                // If there is no chat picture set, that means it is not a channel and the chat picture will be the profile picture of the other user
                "picture" => $chat_picture ?? ($user->id == $users[0]["id"] ? $users[1]["profile_picture"] : $users[0]["profile_picture"])
            ]);
        }

        return redirect(route("chat.index"));
    }

    /**
     * Adds the user to this chat (by creating UserChat of this chat for him)
     */
    public function join(Chat $chat)
    {
        if (UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chat->id)->exists()) {
            abort(400, "You are already in this chat.");
        }

        UserChat::create([
            "user_id" => auth()->user()->id,
            "chat_id" => $chat->id,
            "name" => $chat->name,
            "picture" => UserChat::where("user_id", $chat->chatAdmin->user->id)->where("chat_id", $chat->id)->first()->picture
        ]);

        return redirect(route("chat.index"));
    }

    /**
     * Removes the user from this chat (by deleting UserChat of this chat for him)
     */
    public function leave(Chat $chat)
    {
        if (!UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chat->id)->exists()) {
            abort(400, "You are not in this chat.");
        }

        UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chat->id)->delete();
        ChatAdmin::where("user_id", auth()->user()->id)->where("chat_id", $chat->id)->delete();

        return redirect(route("chat.index"));
    }
}
