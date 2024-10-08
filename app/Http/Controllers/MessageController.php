<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\UserChat;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, Chat $chat)
    {
        $page = $request->query("page", 1);
        $messagesPerPage = 15;

        if (count($chat->messages) == 0) {
            return response()->json([
                "message" => "No messages yet."
            ]);
        }

        if (UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chat->id)->exists()) {
            return response()->json([
                "messages" => $chat
                    ->messages()->with('user')
                    ->latest()
                    ->skip(($page - 1) * $messagesPerPage)
                    ->take($messagesPerPage)
                    ->get()
                    ->reverse()->values()
            ]);
        }

        abort(401);
    }

    public function store(Request $request, Chat $chat)
    {
        $data = $request->validate([
            "message" => "required|min:1"
        ]);

        // Check, if chat id is valid one and if user is in the chat
        $isValidChat = UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chat->id)->exists();
        if ($isValidChat) {
            $message = Message::create(
                [
                    "user_id" => auth()->user()->id,
                    "chat_id" => $chat->id,
                    "content" => $data["message"]
                ]
            );

            // Add User to the message
            $message->user = auth()->user();
            event(new MessageSent($message));

            // Update last message timestamp in chat
            $chat->update(["last_message" => now()->toISOString()]);

            return response()->json([
                "message" => "Message successfully stored.",
            ]);
        } else {
            abort(400, "The user is not in this chat.");
        }
    }
}
