<div class="w-full" x-data="{ searching: @entangle('isSearching') }">
    <div class="relative overflow-hidden bg-slate-900/50 backdrop-blur-2xl border border-white/10 rounded-3xl p-8 shadow-2xl">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
            <div>
                <h2 class="text-4xl font-black bg-gradient-to-r from-cyan-400 to-indigo-400 bg-clip-text text-transparent">BATTLE LOBBY</h2>
                <p class="text-slate-400 font-medium">Ready to test your binary conversion speed?</p>
            </div>
            
            <div class="flex gap-4">
                <div class="bg-white/5 border border-white/5 rounded-2xl px-6 py-3 text-center">
                    <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Your Rank</div>
                    <div class="text-xl font-black text-cyan-400">{{ Auth::user()->rank }}</div>
                </div>
                <div class="bg-white/5 border border-white/5 rounded-2xl px-6 py-3 text-center">
                    <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Level</div>
                    <div class="text-xl font-black text-white">{{ Auth::user()->level }}</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Play Options -->
            <div class="space-y-6">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500 to-indigo-500 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-slate-900 rounded-2xl p-8 border border-white/5 flex flex-col items-center">
                        <div class="w-20 h-20 bg-cyan-500/20 rounded-2xl flex items-center justify-center text-cyan-400 mb-6 drop-shadow-[0_0_15px_rgba(34,211,238,0.3)]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <h3 class="text-2xl font-black mb-2">PVP MULTIPLAYER</h3>
                        <p class="text-slate-400 text-center text-sm mb-8">Match against random players online. Fixed 2 minutes game time.</p>
                        
                        @if(!$isSearching)
                            <button wire:click="findMatch" class="w-full py-4 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black rounded-xl transition-all shadow-lg hover:shadow-cyan-500/25 transform hover:scale-[1.02] active:scale-[0.98]">
                                FIND MATCH
                            </button>
                        @else
                            <div class="w-full space-y-4">
                                <div class="flex items-center justify-center gap-3 py-4 bg-slate-800 text-cyan-400 font-black rounded-xl border border-cyan-500/30">
                                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    SEARCHING...
                                </div>
                                <button wire:click="cancelSearch" class="w-full text-slate-500 hover:text-red-400 text-xs font-bold uppercase tracking-widest transition-colors">
                                    Cancel Search
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <a href="{{ route('game') }}" class="block group">
                    <div class="relative bg-slate-900/40 hover:bg-slate-800/60 rounded-2xl p-6 border border-white/5 flex items-center gap-6 transition-all transform hover:translate-x-2">
                        <div class="w-16 h-16 bg-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-black">SOLO PRACTICE</h3>
                            <p class="text-slate-500 text-sm font-medium">Infinite levels, 30 seconds per level.</p>
                        </div>
                        <div class="text-slate-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Stats & Ranking -->
            <div class="bg-indigo-500/5 rounded-2xl p-8 border border-indigo-500/10">
                <div class="flex items-center gap-3 mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-indigo-400" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    <h3 class="text-xl font-black uppercase tracking-tight">PLAYER STATS</h3>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                            <span>XP PROGRESSION</span>
                            <span>{{ Auth::user()->xp % 1000 }} / 1000</span>
                        </div>
                        <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)] transition-all duration-1000" style="width: {{ (Auth::user()->xp % 1000) / 10 }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Wins</div>
                            <div class="text-2xl font-black text-green-400">{{ Auth::user()->wins }}</div>
                        </div>
                        <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Losses</div>
                            <div class="text-2xl font-black text-red-400">{{ Auth::user()->losses }}</div>
                        </div>
                    </div>
                </div>

                <!-- Leaderboard -->
                <div class="mt-8 pt-8 border-t border-white/5">
                    <livewire:leaderboard />
                </div>
            </div>
        </div>
    </div>
</div>
