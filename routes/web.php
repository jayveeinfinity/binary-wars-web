<?php

use App\Livewire\Lobby;
use App\Livewire\BinaryGame;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('lobby');
});

Route::get('lobby', Lobby::class)
    ->middleware(['auth', 'verified'])
    ->name('lobby');

Route::get('game/{matchId?}', BinaryGame::class)
    ->middleware(['auth', 'verified'])
    ->name('game');

Route::get('dashboard', function () {
    return redirect()->route('lobby');
})->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
