<?php

namespace App\Livewire;

use App\Livewire\Forms\SendMessageForm;
use App\Models\UserChat;
use Livewire\Attributes\On;
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
            "echo-private:chats.{$this->userChat->chat_id},MessageSent" => 'messageSent',
            "switchChat" => "switchChat"
        ];
    }

    public function messageSent()
    {
        $this->dispatch("scrollToBottom");

        $this->dispatch("updateChatTab", $this->userChat->id)->to(ChatTab::class);
    }

    public function switchChat(int $userChatId)
    {
        $userChat = UserChat::find($userChatId);

        if ($userChat) {
            $this->userChat = $userChat;
        } else {
            abort(400, "Invalid chat id.");
        }
    }
}
