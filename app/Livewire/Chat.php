<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Http\Controllers\MessageController;
use App\Livewire\Forms\SendMessageForm;
use App\Models\Message;
use App\Models\UserChat;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Chat extends Component
{
    public UserChat $userChat;

    public SendMessageForm $sendMessageForm;

    public function render()
    {
        return view('livewire.chat');
    }

    public function sendMessage()
    {
        $this->sendMessageForm->store($this->userChat->chat->id);
    }

    // Laravel Echo
    public function getListeners()
    {
        return [
            "echo-private:chats." . $this->userChat->chat_id . ",MessageSent" => 'messageSent',
        ];
    }

    public function messageSent()
    {
        $this->dispatch("scrollToBottom");

        $this->dispatch("updateChatTab", $this->userChat->id)->to(ChatTab::class);
    }

    #[On("switchChat")]
    public function switchChat(int $userChatId)
    {
        $userChat = UserChat::find($userChatId);

        if ($userChat) {
            $this->userChat = UserChat::find($userChatId);
        } else {
            abort(400, "Invalid chat id.");
        }
    }
}
