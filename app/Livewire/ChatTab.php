<?php

namespace App\Livewire;

use Livewire\Component;

class ChatTab extends Component
{
    public $type = "unread";
    public $userChat;

    public function render()
    {
        return view('livewire.chat-tab');
    }

    public function switchChat()
    {
    }
}
