<?php
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NumberGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $number;
    public $raffleId;

    public function __construct($number, $raffleId)
    {
        $this->number = $number;
        $this->raffleId = $raffleId;
    }

    public function broadcastOn()
    {
        return new Channel("raffle.{$this->raffleId}");
    }

    public function broadcastAs()
    {
        return 'number.generated';
    }
}
