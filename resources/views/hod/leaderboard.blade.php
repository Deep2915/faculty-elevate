<x-app-layout>
    <x-slot name="header">Faculty Leaderboard</x-slot>

    <div style="max-width:900px; margin:0 auto;">
        <div class="glass-card" style="padding:0;">
            <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--glass-border); display:flex; align-items:center; justify-content:space-between;">
                <span class="section-title">Department Rankings</span>
                <span class="pill pill-indigo">{{ $profiles->count() }} Faculty</span>
            </div>
            @forelse($profiles as $i => $p)
            <div style="display:flex; align-items:center; gap:1.25rem; padding:1rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.04); transition:background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                {{-- Rank --}}
                <div style="width:44px; text-align:center; font-size:1.25rem; font-weight:800; flex-shrink:0;">
                    @if($i===0)<span class="rank-1">1st</span>@elseif($i===1)<span class="rank-2">2nd</span>@elseif($i===2)<span class="rank-3">3rd</span>
                    @else<span style="font-size:1rem; color:var(--text-muted);">{{ $i+1 }}</span>@endif
                </div>

                {{-- Avatar --}}
                <div class="avatar" style="flex-shrink:0;">{{ strtoupper(substr($p->user->name,0,1)) }}</div>

                {{-- Name + dept --}}
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:700; color:var(--text-primary); font-size:0.9375rem;">{{ $p->user->name }}</div>
                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $p->department ?? 'Department not set' }}</div>
                </div>

                {{-- PI Bar --}}
                <div style="width:180px; flex-shrink:0;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="font-size:0.72rem; color:var(--text-muted);">PI Score</span>
                        <span style="font-size:0.72rem; font-weight:700; color:var(--brand-400);">{{ number_format(($p->performance_index??0)*100,1) }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill {{ ($p->performance_index??0)>=0.8?'green':(($p->performance_index??0)>=0.5?'':'rose') }}" style="width:{{ min(100,($p->performance_index??0)*100) }}%"></div>
                    </div>
                </div>

                {{-- XP + Level --}}
                <div style="text-align:center; flex-shrink:0;">
                    <div style="font-size:0.875rem; font-weight:700; color:var(--accent-amber);">{{ number_format($p->xp??0) }}</div>
                    <div style="font-size:0.7rem; color:var(--text-muted);">XP · Lv {{ $p->level??1 }}</div>
                </div>

                {{-- Report --}}
                <a href="{{ route('hod.report.download', $p->user->id) }}" class="btn btn-ghost btn-xs" style="flex-shrink:0;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    PDF
                </a>
            </div>
            @empty
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">No faculty profiles yet. Create evaluations to populate the leaderboard.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
