<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\GameMatch;
use App\Events\MatchFound;
use Illuminate\Support\Facades\Auth;

class Lobby extends Component
{
    public $isSearching = false;
    public $searchDuration = 0;
    public $currentMatchId = null;

    public function findMatch()
    {
        $user = Auth::user();
        $this->isSearching = true;

        // Look for an existing match with a waiting player
        $existingMatch = GameMatch::where('status', 'searching')
            ->where('player1_id', '!=', $user->id)
            ->first();

        if ($existingMatch) {
            // Join the match
            $existingMatch->update([
                'player2_id' => $user->id,
                'status' => 'playing',
                'started_at' => Carbon::now(),
            ]);

            $this->currentMatchId = $existingMatch->id;
            
            // Broadcast event
            broadcast(new MatchFound($existingMatch))->toOthers();
            
            // Redirect to game
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

    public function getListeners()
    {
        return [
            "echo-private:user." . Auth::id() . ",MatchFound" => 'onMatchFound',
        ];
    }

    public function onMatchFound($event)
    {
        return redirect()->route('game', ['matchId' => $event['match']['id']]);
    }

    public function render()
    {
        return view('livewire.lobby');
    }
}
