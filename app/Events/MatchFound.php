<?php

namespace App\Events;

use App\Models\GameMatch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MatchFound implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $match;

    public function __construct(GameMatch $match)
    {
        $this->match = $match;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->match->player1_id),
            new PrivateChannel('user.' . $this->match->player2_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MatchFound';
    }
}
