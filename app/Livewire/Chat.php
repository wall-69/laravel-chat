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

    #[On("messageSent")]
    public function messageSent()
    {
        $this->dispatch("scrollToBottom");
        $this->dispatch("updateChatTab", $this->userChat->id)->to(ChatTab::class);
    }

    #[On("switchChat")]
    public function switchChat(int $userChatId)
    {
        $oldChatId  = $this->userChat->chat_id;
        $userChat = UserChat::find($userChatId);

        if ($userChat) {
            $this->userChat = $userChat;
            $this->dispatch("switchChannels", oldChannel: "chats.{$oldChatId}", newChannel: "chats.{$userChat->chat_id}");
        } else {
            abort(400, "Invalid chat id.");
        }
    }
}
