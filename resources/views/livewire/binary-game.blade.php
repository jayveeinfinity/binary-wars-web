<div class="min-h-screen bg-slate-950 text-white flex flex-col items-center justify-center p-4 font-sans selection:bg-cyan-500/30"
     x-data="{ 
        timeLeft: @entangle('timeLeft'),
        isGameOver: @entangle('isGameOver'),
        initTimer() {
            let timer = setInterval(() => {
                if (this.timeLeft > 0 && !this.isGameOver) {
                    $wire.tick();
                } else {
                    clearInterval(timer);
                }
            }, 1000);
        }
     }"
     x-init="initTimer()">
    
    <!-- Loading / Versus Screen -->
    @if($isLoading && $isMultiplayer)
        <div class="fixed inset-0 z-50 bg-slate-950 flex flex-col items-center justify-center animate-in fade-in duration-500">
            <div class="relative flex items-center justify-center gap-20 mb-12">
                <!-- Player 1 -->
                <div class="flex flex-col items-center gap-4 animate-in slide-in-from-left-10 duration-700">
                    <div class="w-32 h-32 rounded-3xl bg-cyan-500/20 border-2 border-cyan-500 flex items-center justify-center text-cyan-400 shadow-[0_0_30px_rgba(34,211,238,0.3)]">
                        <span class="text-4xl font-black">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="text-xl font-black">{{ Auth::user()->name }}</div>
                    <div class="px-4 py-1 bg-cyan-500/10 rounded-full border border-cyan-500/20 text-xs font-bold text-cyan-400">LVL {{ Auth::user()->level }}</div>
                </div>

                <!-- VS -->
                <div class="text-7xl font-black italic bg-gradient-to-b from-white to-slate-700 bg-clip-text text-transparent animate-bounce">VS</div>

                <!-- Player 2 -->
                <div class="flex flex-col items-center gap-4 animate-in slide-in-from-right-10 duration-700">
                    <div class="w-32 h-32 rounded-3xl bg-indigo-500/20 border-2 border-indigo-500 flex items-center justify-center text-indigo-400 shadow-[0_0_30px_rgba(99,102,241,0.3)]">
                        <span class="text-4xl font-black">{{ substr($opponent->name, 0, 1) }}</span>
                    </div>
                    <div class="text-xl font-black">{{ $opponent->name }}</div>
                    <div class="px-4 py-1 bg-indigo-500/10 rounded-full border border-indigo-500/20 text-xs font-bold text-indigo-400">LVL {{ $opponent->level }}</div>
                </div>
            </div>

            <div class="text-center">
                <div class="text-slate-500 font-bold uppercase tracking-[0.5em] mb-4">Starting In</div>
                <div class="text-8xl font-black text-white tabular-nums">{{ $loadingCountdown }}</div>
            </div>
        </div>
    @endif

    <!-- Background Glows -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-cyan-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-600/20 blur-[120px] rounded-full"></div>
    </div>

    <!-- Game Container -->
    <div class="relative w-full max-w-xl bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl overflow-hidden {{ $isLoading && $isMultiplayer ? 'opacity-0 scale-95' : 'opacity-100 scale-100' }} transition-all duration-700">
        
        @if($isGameOver)
            <div class="absolute inset-0 z-40 bg-slate-950/90 backdrop-blur-md flex flex-col items-center justify-center p-8 text-center animate-in zoom-in duration-300">
                <div class="w-24 h-24 bg-gradient-to-tr from-cyan-400 to-indigo-600 rounded-full flex items-center justify-center text-white mb-6 shadow-[0_0_40px_rgba(34,211,238,0.5)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                </div>
                
                <h2 class="text-3xl font-black mb-2">{{ $isMultiplayer ? ($score > $opponentScore ? 'VICTORY!' : ($score < $opponentScore ? 'DEFEAT' : 'DRAW')) : 'SESSION ENDED' }}</h2>
                <p class="text-slate-400 font-bold mb-8 italic">"{{ $status }}"</p>
                
                <div class="grid grid-cols-2 gap-4 w-full mb-8">
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Final Score</div>
                        <div class="text-3xl font-black text-white">{{ number_format($score) }}</div>
                    </div>
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">XP Gained</div>
                        <div class="text-3xl font-black text-cyan-400">+{{ number_format($score) }}</div>
                    </div>
                </div>

                <div class="space-y-3 w-full">
                    <button wire:click="init" class="w-full bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black py-4 rounded-xl transition-all shadow-lg hover:shadow-cyan-500/25">
                        PLAY AGAIN
                    </button>
                    <a href="{{ route('lobby') }}" class="block w-full bg-slate-800 hover:bg-slate-700 text-white font-black py-4 rounded-xl transition-all">
                        BACK TO LOBBY
                    </a>
                </div>
            </div>
        @endif

        <!-- Header Info -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex flex-col">
                <span class="text-slate-400 text-xs uppercase tracking-widest font-bold">Progress</span>
                <span class="text-2xl font-black bg-gradient-to-r from-cyan-400 to-indigo-400 bg-clip-text text-transparent">{{ $isMultiplayer ? 'PVP MATCH' : 'LEVEL ' . $level }}</span>
            </div>
            
            <div class="flex flex-col items-end">
                <span class="text-slate-400 text-xs uppercase tracking-widest font-bold">Total Score</span>
                <span class="text-2xl font-black text-white drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]">{{ number_format($score) }}</span>
            </div>
        </div>

        <!-- Timer Circle -->
        <div class="flex justify-center mb-8 relative">
            <div class="relative w-32 h-32">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-800" />
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" 
                            class="text-cyan-500 transition-all duration-1000 ease-linear"
                            stroke-dasharray="364.4"
                            style="stroke-dashoffset: {{ 364.4 * (1 - ($timeLeft / $defaultTime)) }}" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black {{ $timeLeft < 10 ? 'text-red-500 animate-pulse' : 'text-white' }}">{{ $timeLeft }}</span>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-tighter">Seconds</span>
                </div>
            </div>
        </div>

        <h1 class="text-center text-4xl font-black mb-2 tracking-tight">
            Decimal <span class="text-cyan-400">➜</span> Binary
        </h1>
        <p class="text-center text-slate-400 text-sm mb-6">Convert the decimal value to 8-bit binary</p>

        <!-- Current Target -->
        <div class="bg-white/5 border border-white/5 rounded-2xl p-6 mb-8 text-center group transition-all hover:bg-white/10">
            <div class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Target Number</div>
            <div class="text-6xl font-black text-cyan-400 tabular-nums tracking-tighter transition-all group-hover:scale-110 drop-shadow-[0_0_15px_rgba(34,211,238,0.4)]">
                {{ $remainingDecimal }}
            </div>
        </div>

        <!-- Binary Grid -->
        <div class="grid grid-cols-8 gap-3 mb-8">
            @foreach($bits as $index => $bit)
                <div class="flex flex-col items-center gap-2">
                    <button wire:click="toggleBit({{ $index }})"
                            class="w-full aspect-square rounded-xl flex items-center justify-center text-xl font-black transition-all duration-200 
                                   {{ $bit ? 'bg-green-500 text-slate-900 shadow-[0_0_20px_rgba(34,197,94,0.4)]' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}
                                   {{ $selectedBitIndex === $index ? 'ring-4 ring-cyan-500/50' : '' }}
                                   {{ $isGameOver ? 'cursor-not-allowed opacity-50' : '' }}">
                        {{ $bit }}
                    </button>
                    <span class="text-[10px] font-bold text-slate-600">{{ pow(2, 7 - $index) }}</span>
                </div>
            @endforeach
        </div>

        <!-- Status Message -->
        <div class="text-center h-8 font-bold {{ $statusColor }} text-lg mb-6">
            {{ $status }}
        </div>

        <!-- Controls -->
        <div class="flex flex-col gap-4">
            @if($isGameOver)
                <button wire:click="init" class="w-full bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-black py-4 rounded-2xl shadow-xl transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    TRY AGAIN
                </button>
            @endif
            
            <a href="{{ route('dashboard') }}" class="text-center text-slate-500 text-sm font-bold hover:text-white transition-colors">
                RETURN TO LOBBY
            </a>
        </div>
    </div>

    <!-- Opponent Score (for Multiplayer) -->
    @if($isMultiplayer)
        <div class="mt-8 w-full max-w-xl bg-slate-900/40 backdrop-blur-md border border-white/5 p-4 rounded-2xl flex justify-between items-center animate-in slide-in-from-bottom-5 fade-in duration-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full {{ $isOpponentDead ? 'bg-red-500/20 border-red-500/40 text-red-400' : 'bg-indigo-500/20 border-indigo-500/40 text-indigo-400' }} flex items-center justify-center">
                    @if($isOpponentDead)
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    @endif
                </div>
                <div>
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $isOpponentDead ? 'Eliminated' : 'Competing' }}</div>
                    <div class="text-sm font-black">{{ $opponent->name }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Score</div>
                <div class="text-xl font-black tabular-nums transition-all {{ $isOpponentDead ? 'text-slate-500' : 'text-white' }}">{{ number_format($opponentScore) }}</div>
            </div>
        </div>
    @endif

</div>
