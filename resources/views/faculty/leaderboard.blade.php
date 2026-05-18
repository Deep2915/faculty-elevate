<x-app-layout>
    <x-slot name="header">Leaderboard</x-slot>

    @if($myRank)
    <div class="glass-card" style="margin-bottom:1.5rem; border-color:rgba(99,102,241,0.4); background:rgba(99,102,241,0.08);">
        <div style="display:flex; align-items:center; gap:1rem;">
            <div style="font-size:2.5rem;">{{ $myRank===1?'🥇':($myRank===2?'🥈':($myRank===3?'🥉':'🎖️')) }}</div>
            <div>
                <div style="font-size:1rem; font-weight:700; color:var(--text-primary);">Your Global Rank: #{{ $myRank }}</div>
                <div style="font-size:0.8125rem; color:var(--text-muted);">PI: {{ number_format(($myProfile->performance_index??0)*100,1) }}% · XP: {{ number_format($myProfile->xp??0) }} · Level {{ $myProfile->level??1 }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="glass-card" style="padding:0;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--glass-border);">
            <span class="section-title">🏆 Institution Leaderboard</span>
        </div>
        @forelse($profiles as $i => $p)
        @php $isMe = (string)$p->user_id === (string)auth()->id(); @endphp
        <div style="display:flex; align-items:center; gap:1.25rem; padding:0.875rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.03); {{ $isMe?'background:rgba(99,102,241,0.07);':'' }} transition:background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='{{ $isMe?'rgba(99,102,241,0.07)':'transparent' }}'">
            <div style="width:36px;text-align:center;font-size:1.1rem;font-weight:800;flex-shrink:0;">
                @if($i===0)🥇@elseif($i===1)🥈@elseif($i===2)🥉
                @else<span style="color:var(--text-muted);font-size:0.9rem;">{{ $i+1 }}</span>@endif
            </div>
            <div class="avatar" style="flex-shrink:0;{{ $isMe?'border:2px solid var(--brand-500);':'' }}">{{ strtoupper(substr($p->user->name,0,1)) }}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:{{ $isMe?'800':'600' }}; color:var(--text-primary); font-size:0.875rem;">
                    {{ $p->user->name }} {{ $isMe ? '(You)' : '' }}
                </div>
                <div style="font-size:0.72rem; color:var(--text-muted);">Level {{ $p->level??1 }} · {{ $p->department ?? '' }}</div>
            </div>
            <div style="width:160px;flex-shrink:0;">
                <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                    <span style="font-size:0.68rem;color:var(--text-muted);">PI</span>
                    <span style="font-size:0.68rem;font-weight:700;color:var(--brand-400);">{{ number_format(($p->performance_index??0)*100,1) }}%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:{{ min(100,($p->performance_index??0)*100) }}%;"></div></div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div style="font-weight:700;color:var(--accent-amber);font-size:0.875rem;">{{ number_format($p->xp??0) }}</div>
                <div style="font-size:0.68rem;color:var(--text-muted);">XP</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:3rem;color:var(--text-muted);">No rankings yet.</div>
        @endforelse
    </div>
</x-app-layout>
