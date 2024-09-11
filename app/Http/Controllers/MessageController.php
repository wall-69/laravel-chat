<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\UserChat;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class MessageController extends Controller
{

    public function store(Request $request, int $chatId)
    {
        $data = $request->validate([
            "message" => "required|min:1"
        ]);

        // Check, if chat id is valid one and if user is in the chat
        $isValidChat = UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chatId)->exists();
        if ($isValidChat) {
            $message = Message::create(
                [
                    "user_id" => auth()->user()->id,
                    "chat_id" => $chatId,
                    "content" => $data["message"]
                ]
            );

            event(new MessageSent($message));

            return back();
        } else {
            abort(400, "Invalid chat id.");
        }
    }
}
