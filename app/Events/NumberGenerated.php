<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class NumberGenerated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $number;
    public $raffle_id;

    public function __construct($number, $raffle_id)
    {
        $this->number = $number;
        $this->raffle_id = $raffle_id;
    }

    public function broadcastOn()
    {
        return new Channel('raffle.' . $this->raffle_id);
    }

    public function broadcastAs()
    {
        return 'number.generated';
    }
}
