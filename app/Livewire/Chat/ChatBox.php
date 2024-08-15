<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Message;
use Livewire\Attributes\Validate;

class ChatBox extends Component
{
    public $selectedConversation;
    #[Validate('required')]
    public $body;

    public function sendMessage()
    {
        $this->validate(['body'=>'required|string']);

        // dd($this->selectedConversation->getReceiver()->id . ' ' .$this->selectedConversation->id .' '.auth()->id());
        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'sender_id' => auth()->id(),
            'body' => $this->body
        ]);

        $this->reset('body');

        // dd($createdMessage);
    }
    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
