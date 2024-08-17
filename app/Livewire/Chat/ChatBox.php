<?php

namespace App\Livewire\Chat;

use App\Events\ChatSendEvent;
use Livewire\Component;
use App\Models\Message;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class ChatBox extends Component
{
    public $selectedConversation;
    #[Validate('required|string')]
    public $body;
    public $loadedMessages;
    public $paginate_var = 10;

    public function getListeners(){
        $auth_id = auth()->id();

        return[
            'loadMore',
            "echo-private:chat-channel.{$auth_id},ChatSendEvent"=>'broadcastedNotifications'
        ];
    }

    public function broadcastedNotifications($event)
    {
        // dd($event);
        $this->loadMessages();
    }

    // public $listeners = [
    //     'loadMore'
    // ];

    // #[On('echo-private:messages,MessengerEvent')]
    // #[On('echo:chat-channel,ChatSendEvent')]
    // public function onChatSendEvent($event){
        // dd($this->selectedConversation);
        // dd($event);
    //     $this->loadMessages();
    // }

    public function loadMore() : void
    {
        // increment paginate variable
        $this->paginate_var += 10;

        // call loadMessages
        $this->loadMessages();

        $this->dispatch('update-chat-height');
    }

    public function loadMessages()
    {
        // get count()
        $count = Message::where('conversation_id',$this->selectedConversation->id)->count();
        // Skip and query
        $this->loadedMessages = Message::where('conversation_id',$this->selectedConversation->id)
        ->skip($count-$this->paginate_var)
        ->take($this->paginate_var)
        ->get();

        return $this->loadedMessages;
    }

    public function sendMessage()
    {
        // $this->validate(['body'=>'required|string']);

        // dd($this->selectedConversation->getReceiver()->id . ' ' .$this->selectedConversation->id .' '.auth()->id());
        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'sender_id' => auth()->id(),
            'body' => $this->body
        ]);

        // Broadcast message to Event
        broadcast(new ChatSendEvent($createdMessage))->toOthers();

        $this->reset('body');

        // scroll to bottom
        $this->dispatch('scroll-bottom');

        $this->loadedMessages->push($createdMessage);

        // Update conversation
        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        // Refresh chatlist
        $this->dispatch('chat.chat-list','refresh');
    }

    public function mount()
    {
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
