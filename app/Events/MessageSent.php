<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $senderId;
    public string $htmlSent, $htmlReceived;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly Message $message
    ) {
        $this->senderId = $message->user->id;

        $this->htmlSent = View::make("components.chat-sent-message", [
            "message" => $message->content,
        ])->render();
        $this->htmlReceived = View::make("components.chat-received-message", [
            "userId" => $this->senderId,
            "message" => $message->content,
        ])->render();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chats.' . $this->message->chat_id),
        ];
    }
}
