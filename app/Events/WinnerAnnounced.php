<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WinnerAnnounced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player_id;
    public $raffle_id;
    public $prize;

    public function __construct($player_id, $raffle_id, $prize)
    {
        $this->player_id = $player_id;
        $this->raffle_id = $raffle_id;
        $this->prize = $prize;
    }

    public function broadcastOn()
    {
        return new Channel('raffle-winner');
    }

    public function broadcastAs()
    {
        return 'winner.announced';
    }
}
