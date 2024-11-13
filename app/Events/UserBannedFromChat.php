<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserChatBan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserBannedFromChat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public readonly int $chatId;
    public readonly array $ban;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $chatId,
        UserChatBan $userChatBan
    ) {
        $this->chatId = $chatId;
        $this->ban = $userChatBan->toBroadcastArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("chats." . $this->chatId),
        ];
    }
}
