<x-app-layout>
    <x-slot name="header">Workshops</x-slot>

    {{-- Status Tabs --}}
    <div style="display:flex; gap:0.5rem; margin-bottom:1.25rem; flex-wrap:wrap;">
        @foreach(['upcoming'=>'Upcoming','ongoing'=>'Ongoing','completed'=>'Completed'] as $s => $label)
        <a href="{{ route('faculty.workshops.index', ['status'=>$s]) }}"
           class="btn {{ request('status',$s==='upcoming'?'upcoming':'')!=='' && request('status')===$s ? 'btn-primary' : 'btn-ghost' }}"
           style="{{ request('status','upcoming')===$s ? '' : 'opacity:0.7;' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Cards Grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(310px,1fr)); gap:1.25rem; margin-bottom:1.5rem;">
        @forelse($workshops as $ws)
        <div class="glass-card" style="display:flex; flex-direction:column;">
            {{-- Header --}}
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.875rem;">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <span class="pill {{ $ws->status==='upcoming'?'pill-indigo':($ws->status==='ongoing'?'pill-amber':'pill-green') }}">
                    {{ ucfirst($ws->status) }}
                </span>
            </div>

            <div style="font-weight:700; color:var(--text-primary); font-size:0.9375rem; margin-bottom:0.25rem;">{{ $ws->title }}</div>
            <div style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.875rem;">by {{ $ws->facilitator }}</div>
            <p style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:1rem; line-height:1.5; flex:1;">{{ Str::limit($ws->description, 100) }}</p>

            {{-- Meta Pills --}}
            <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-bottom:1rem;">
                <span class="pill pill-cyan">{{ $ws->category }}</span>
                <span class="pill pill-gray">{{ optional($ws->schedule_date)->format('d M Y') }}</span>
                <span class="pill pill-gray">{{ $ws->duration_hours }}h</span>
                <span class="pill pill-gray">{{ $ws->capacity }} seats</span>
                <span class="pill pill-amber">+{{ $ws->xp_reward }} XP</span>
            </div>

            {{-- Register --}}
            @if($ws->status === 'upcoming' || $ws->status === 'ongoing')
            <form method="POST" action="{{ route('faculty.workshops.register', $ws->id) }}">
                @csrf
                <button class="btn btn-primary" style="width:100%;">
                    Register & Earn {{ $ws->xp_reward }} XP
                </button>
            </form>
            @else
            <div class="btn btn-ghost" style="width:100%; cursor:default; justify-content:center; opacity:0.5;">Workshop Completed</div>
            @endif
        </div>
        @empty
        <div class="glass-card" style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-muted);">
            <div style="font-size:2rem; margin-bottom:0.75rem; color:var(--brand-400);">&#128236;</div>
            No {{ $status }} workshops available. Check back soon!
        </div>
        @endforelse
    </div>

    <div>{{ $workshops->links() }}</div>
</x-app-layout>
