<div class="space-y-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-black uppercase tracking-tight text-slate-400">Top Warriors</h3>
        <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">Global Rank</span>
    </div>

    @foreach($players as $index => $player)
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 transition-all hover:bg-white/10 group">
            <div class="w-8 h-8 flex items-center justify-center font-black text-xs
                        {{ $index === 0 ? 'text-yellow-400' : ($index === 1 ? 'text-slate-300' : ($index === 2 ? 'text-orange-400' : 'text-slate-600')) }}">
                #{{ $index + 1 }}
            </div>
            
            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-sm font-black text-white group-hover:bg-indigo-500/20 transition-colors">
                {{ substr($player->name, 0, 1) }}
            </div>

            <div class="flex-1">
                <div class="text-sm font-black">{{ $player->name }}</div>
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $player->rank }}</div>
            </div>

            <div class="text-right">
                <div class="text-sm font-black text-cyan-400">{{ number_format($player->xp) }}</div>
                <div class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">XP</div>
            </div>
        </div>
    @endforeach

    @if($players->isEmpty())
        <div class="text-center py-8 text-slate-600 font-bold italic text-sm">
            No players matching yet...
        </div>
    @endif
</div>
