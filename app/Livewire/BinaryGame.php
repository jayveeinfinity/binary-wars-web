<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\GameMatch;
use Livewire\Attributes\On;
use App\Events\ScoreUpdated;
use App\Events\PlayerEliminated;
use Illuminate\Support\Facades\Auth;

class BinaryGame extends Component
{
    public $level = 0;
    public $score = 0;
    public $selectedBitIndex = 0;
    public $moves = 0;
    public $BIT_COUNT = 8;
    public $originalDecimal = 0;
    public $remainingDecimal = 0;
    public $movesNeeded = 0;
    public $bits = [];
    public $timeLeft = 30;
    public $defaultTime = 30;
    public $isGameOver = false;
    public $status = "";
    public $statusColor = "text-yellow-400";
    
    // Multiplayer specific
    public $isMultiplayer = false;
    public $matchId = null;
    public $opponentScore = 0;
    public $isOpponentDead = false;
    public $isLoading = true;
    public $loadingCountdown = 5;
    public $match = null;
    public $opponent = null;

    public function mount($matchId = null)
    {
        if ($matchId) {
            $this->isMultiplayer = true;
            $this->matchId = $matchId;
            $this->match = GameMatch::with(['player1', 'player2'])->find($matchId);
            $this->opponent = ($this->match->player1_id === Auth::id()) ? $this->match->player2 : $this->match->player1;
            $this->defaultTime = 120; // 2 minutes for multiplayer
        } else {
            $this->isLoading = false; // Start immediately for single player
        }
        
        $this->init();
    }

    public function init()
    {
        $this->score = 0;
        $this->level = 0;
        $this->timeLeft = $this->defaultTime;
        $this->isGameOver = false;
        $this->newGame();
    }

    public function startMatch()
    {
        $this->isLoading = false;
    }

    public function newGame()
    {
        $this->level++;
        $this->originalDecimal = rand(1, 255);
        $this->remainingDecimal = $this->originalDecimal;
        $this->bits = array_fill(0, $this->BIT_COUNT, 0);
        $this->selectedBitIndex = 0;
        $this->moves = 0;
        $this->setMovesNeeded($this->originalDecimal);
        $this->status = "Keep going...";
        $this->statusColor = "text-yellow-400";
    }

    private function setMovesNeeded($decimal)
    {
        $count = 0;
        $temp = $decimal;
        while ($temp > 0) {
            $temp &= ($temp - 1);
            $count++;
        }
        $this->movesNeeded = $count;
    }

    public function toggleBit($index)
    {
        if ($this->isGameOver || $this->isLoading) return;

        $bitValue = pow(2, $this->BIT_COUNT - 1 - $index);

        if ($this->bits[$index] === 0) {
            $this->bits[$index] = 1;
            $this->remainingDecimal -= $bitValue;
            $this->moves++;
        } else {
            $this->bits[$index] = 0;
            $this->remainingDecimal += $bitValue;
        }

        $this->checkAnswer();
    }

    public function checkAnswer()
    {
        if ($this->remainingDecimal === 0) {
            $this->status = "✅ Correct!";
            $this->statusColor = "text-green-400";
            $this->computeScore();
            
            $this->dispatch('play-sfx', name: 'correct');
            $this->newGame();
        } elseif ($this->remainingDecimal < 0) {
            $this->status = "⚠ Too much! Go back.";
            $this->statusColor = "text-red-400";
            if (!$this->isMultiplayer) {
                $this->endGame(false);
            }
        }
    }

    public function computeScore()
    {
        $timeTaken = $this->defaultTime - $this->timeLeft;
        $timeBonus = max(0, $this->timeLeft) * 10;
        $efficiencyBonus = max(0, $this->movesNeeded * 2 - $this->moves) * 50;
        $difficultyMultiplier = $this->BIT_COUNT / 4;

        $newScore = floor((10 + $timeBonus + $efficiencyBonus) * $difficultyMultiplier);
        $this->score += $newScore;
        
        if ($this->isMultiplayer) {
            $this->broadcastScore();
        }
    }

    public function broadcastScore()
    {
        broadcast(new ScoreUpdated($this->matchId, Auth::id(), $this->score))->toOthers();
    }

    public function tick()
    {
        if ($this->isLoading) {
            $this->loadingCountdown--;
            if ($this->loadingCountdown <= 0) {
                $this->startMatch();
            }
            return;
        }

        if ($this->isGameOver) return;

        $this->timeLeft--;
        if ($this->timeLeft <= 0) {
            $this->endGame(false);
        }
    }

    #[On("echo-private:match.{matchId},ScoreUpdated")]
    public function onScoreUpdated($event)
    {
        if ($event['userId'] != Auth::id()) {
            $this->opponentScore = $event['score'];
        }
    }

    #[On("echo-private:match.{matchId},PlayerEliminated")]
    public function onPlayerEliminated($event)
    {
        if ($event['userId'] != Auth::id()) {
            $this->isOpponentDead = true;
            // The game continues until current player dies too or time ends
        }
    }

    public function endGame($won)
    {
        if ($this->isGameOver) return;
        
        $this->isGameOver = true;
        $this->status = "Game Over";
        $this->statusColor = "text-red-400";
        $this->dispatch('play-sfx', name: 'wrong');
        
        if ($this->isMultiplayer) {
            broadcast(new PlayerEliminated($this->matchId, Auth::id()))->toOthers();
            $this->handleMatchEnd();
        } else {
            $this->updateUserXP($this->score);
        }
    }

    public function updateUserXP($amount, $isWin = false)
    {
        $user = Auth::user();
        if ($user) {
            $user->xp += $amount;
            if ($isWin) $user->wins++;
            else $user->losses++;

            // Simple level up: 1000 XP per level
            $user->level = floor($user->xp / 1000) + 1;
            
            // Assign Rank
            if ($user->level < 5) $user->rank = "Recruit";
            elseif ($user->level < 10) $user->rank = "Soldier";
            elseif ($user->level < 20) $user->rank = "Veteran";
            elseif ($user->level < 50) $user->rank = "Elite";
            else $user->rank = "Master";

            $user->save();
        }
    }

    public function handleMatchEnd()
    {
        // For multiplayer, we check if both are dead to show results
        if ($this->isOpponentDead || $this->timeLeft <= 0) {
            $winnerId = $this->score > $this->opponentScore ? Auth::id() : ($this->score < $this->opponentScore ? $this->opponent->id : null);
            
            $this->match->update([
                'winner_id' => $winnerId,
                'score1' => ($this->match->player1_id === Auth::id()) ? $this->score : $this->opponentScore,
                'score2' => ($this->match->player2_id === Auth::id()) ? $this->score : $this->opponentScore,
                'status' => 'finished',
                'ended_at' => Carbon::now(),
            ]);

            $this->updateUserXP($this->score, $winnerId === Auth::id());
        }
    }

    public function render()
    {
        return view('livewire.binary-game');
    }
}
