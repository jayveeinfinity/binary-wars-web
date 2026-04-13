<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Leaderboard extends Component
{
    public function render()
    {
        $topPlayers = User::orderBy('xp', 'desc')
            ->take(10)
            ->get();

        return view('livewire.leaderboard', [
            'players' => $topPlayers
        ]);
    }
}
