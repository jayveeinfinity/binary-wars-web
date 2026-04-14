<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\GameMatch;
use App\Events\MatchFound;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Lobby extends Component
{
    public $isSearching = false;
    public $searchDuration = 0;
    public $currentMatchId = null;
    public $userId = null;

    public function mount()
    {
        $this->userId = Auth::id();
    }

    public function findMatch()
    {
        $user = Auth::user();
        $this->isSearching = true;

        // Look for an existing match with a waiting player that has been updated recently (within last 30 seconds)
        $existingMatch = GameMatch::where('status', 'searching')
            ->where('player1_id', '!=', $user->id)
            ->where('updated_at', '>=', Carbon::now()->subSeconds(30))
            ->first();

        if ($existingMatch) {
            // Join the match
            $existingMatch->update([
                'player2_id' => $user->id,
                'status' => 'playing',
                'started_at' => Carbon::now(),
            ]);

            $this->currentMatchId = $existingMatch->id;
            
            // Broadcast event to the other player (player1)
            broadcast(new MatchFound($existingMatch))->toOthers();
            
            // Redirect current player (player2) to game
            return redirect()->route('game', ['matchId' => $existingMatch->id]);
        } else {
            // Create a new searching match
            $newMatch = GameMatch::create([
                'player1_id' => $user->id,
                'status' => 'searching',
            ]);
            
            $this->currentMatchId = $newMatch->id;
        }
    }

    public function cancelSearch()
    {
        if ($this->currentMatchId) {
            $match = GameMatch::find($this->currentMatchId);
            if ($match && $match->status === 'searching') {
                $match->delete();
            }
        }
        $this->isSearching = false;
        $this->currentMatchId = null;
    }

    // Keep the match updated so others know we are still searching
    public function heartbeat()
    {
        if ($this->isSearching && $this->currentMatchId) {
            $match = GameMatch::find($this->currentMatchId);
            if ($match) {
                $match->touch();
            }
        }
    }

    #[On("echo-private:user.{userId},MatchFound")]
    public function onMatchFound($event)
    {
        $matchId = is_array($event['match']) ? $event['match']['id'] : $event['match']->id;
        return redirect()->route('game', ['matchId' => $matchId]);
    }

    public function render()
    {
        return view('livewire.lobby');
    }
}
