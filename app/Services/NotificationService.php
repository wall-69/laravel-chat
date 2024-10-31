<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;

class NotificationService
{
    /**
     * Sends a notification in a chat with specified message.
     */
    public function chat(Chat $chat, string $message)
    {
        // Create the Message
        $message = Message::create(
            [
                "user_id" => null,
                "chat_id" => $chat->id,
                "type" => "notification",
                "content" => $message
            ]
        );

        // Broadcast MessageSent event
        event(new MessageSent($message));

        // Update last message timestamp in chat
        $chat->update(["last_message" => now()->toISOString()]);
    }
}
