<?php

namespace App\Livewire\Forms;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\UserChat;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SendMessageForm extends Form
{
    #[Validate("required|min:1")]
    public $message = "";

    public function store(int $chatId)
    {
        $this->validate();

        // Check, if chat id is valid one and if user is in the chat
        $isValidChat = UserChat::where("user_id", auth()->user()->id)->where("chat_id", $chatId)->exists();
        if ($isValidChat) {
            $message = Message::create(
                [
                    "user_id" => auth()->user()->id,
                    "chat_id" => $chatId,
                    "content" => $this->message
                ]
            );

            event(new MessageSent($message));

            $this->reset("message");
        } else {
            abort(400, "Invalid chat id.");
        }
    }
}
