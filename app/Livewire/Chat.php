<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Http\Controllers\MessageController;
use App\Models\Message;
use App\Models\UserChat;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Chat extends Component
{
    public $userChat;

    #[Validate("required")]
    public $message = "";

    public function render()
    {
        return view('livewire.chat');
    }

    public function sendMessage()
    {
        $chatId = $this->userChat->chat->id;

        // Check, if chat id is valid one and if user is in the chat
        $validChat = UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chatId)->exists();
        if ($validChat) {
            $message = Message::create(
                [
                    "user_id" => auth()->user()->id,
                    "chat_id" => $chatId,
                    "content" => $this->message
                ]
            );

            event(new MessageSent($message));

            $this->message = "";
        } else {
            return abort(400, "Invalid chat id.");
        }
    }

    // Laravel Echo
    public function getListeners()
    {
        return [
            // "echo-private:chats." . $this->userChat->chat_id . ",MessageSent" => '$refresh',
            "echo-private:chats." . $this->userChat->chat_id . ",MessageSent" => 'messageSent',
        ];
    }

    public function messageSent()
    {
        $this->dispatch("scrollToBottom");
    }
}
