<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSendEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $createdMessage;
    /**
     * Create a new event instance.
     */
    public function __construct($createdMessage)
    {
        $this->createdMessage = $createdMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */


    public function broadcastOn(): array
    {
        // dd($this->createdMessage);
        return [
            new PrivateChannel('chat-channel.'.$this->createdMessage->receiver_id),
        ];
        // return [
        //     new Channel('chat-channel'),
        // ];
    }
}
