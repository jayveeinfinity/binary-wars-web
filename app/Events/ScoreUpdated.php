<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $matchId;
    public $userId;
    public $score;

    public function __construct($matchId, $userId, $score)
    {
        $this->matchId = $matchId;
        $this->userId = $userId;
        $this->score = $score;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('match.' . $this->matchId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ScoreUpdated';
    }
}
