<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class WinnerAnnounced implements ShouldBroadcast
{
    use SerializesModels;

    public $player_id;
    public $bingo_card_id;
    public $prize;

    public function __construct($player_id, $bingo_card_id, $prize)
    {
        $this->player_id = $player_id;
        $this->bingo_card_id = $bingo_card_id;
        $this->prize = $prize;
    }

    public function broadcastOn()
    {
        return new Channel("raffle-winner");
    }

    public function broadcastAs()
    {
        return "winner.announced";
    }
}
