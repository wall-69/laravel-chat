<?php

namespace App\Livewire;

use App\Models\UserChat;
use Livewire\Component;

class ChatTab extends Component
{
    public string $type = "unread";
    public UserChat $userChat;

    public function render()
    {
        return view('livewire.chat-tab');
    }

    public function getListeners()
    {
        return [
            "updateChatTab" => '$refresh'
        ];
    }

    public function switchChat()
    {
        $this->dispatch("switchChat", $this->userChat->id)->to(Chat::class);
    }
}
