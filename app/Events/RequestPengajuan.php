<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestPengajuan implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message,$polis_id,$transaction_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message,$polis_id,$transaction_id)
    {
        $this->message = $message;
        $this->polis_id = $polis_id;
        $this->transaction_id = $transaction_id;
    }

    public function broadcastOn()
    {
        return ['pengajuan'];
    }

    public function broadcastAs()
    {
        return 'generate';
    }
}
