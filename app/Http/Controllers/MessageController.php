<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\UserChat;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // TODO: should be API?

    /**
     * Returns `$messagesPerPage` messages based on the specified page.
     */
    public function index(Request $request, Chat $chat)
    {
        $messagesPerPage = 15;

        // Get the page
        $page = $request->query("page", 1);

        // Check, if there are no messages in the chat
        if (count($chat->messages) == 0) {
            return response()->json([
                "message" => "No messages yet."
            ]);
        }

        // Check, if the user is in this chat
        if (UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chat->id)->exists()) {
            // Return the messages with the user model alongside, skip all messages that are already loaded, then take $messagesPerPage messages
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

        // Abort, if the user is not in the chat
        abort(401);
    }

    /**
     * Store the message with specified data.
     */
    public function store(Request $request, Chat $chat)
    {
        // Validate
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
                    "type" => "user",
                    "content" => $data["message"]
                ]
            );

            // Add User to the message
            $message->user = auth()->user();
            // Broadcast MessageSent event
            event(new MessageSent($message));

            // Update last message timestamp in chat
            $chat->update(["last_message" => now()->toISOString()]);

            return response()->json([
                "message" => "Message was successfully stored.",
            ]);
        } else {
            abort(400, "The user is not in this chat.");
        }
    }
}
