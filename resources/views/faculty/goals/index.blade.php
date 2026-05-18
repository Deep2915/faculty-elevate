<x-app-layout>
    <x-slot name="header">My Goals</x-slot>

    <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-bottom:1.25rem;">
        <a href="{{ route('faculty.roadmap') }}" class="btn btn-ghost">🗺️ Roadmap View</a>
        <button onclick="document.getElementById('createGoalModal').style.display='flex'" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Goal
        </button>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.25rem;">
        @forelse($goals as $goal)
        @php $pct = (float)($goal->completion_percentage ?? 0); @endphp
        <div class="glass-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.875rem;">
                <div style="font-weight:700; color:var(--text-primary); font-size:0.9375rem; line-height:1.4;">{{ $goal->title }}</div>
                <span class="pill {{ $goal->status==='completed'?'pill-green':'pill-indigo' }}" style="flex-shrink:0; margin-left:0.5rem;">
                    {{ ucfirst($goal->status) }}
                </span>
            </div>
            <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.875rem;">{{ $goal->description }}</p>
            <div style="display:flex; justify-content:space-between; margin-bottom:0.375rem;">
                <span style="font-size:0.72rem; color:var(--text-muted);">Progress</span>
                <span style="font-size:0.72rem; font-weight:700; color:var(--brand-400);">{{ $pct }}%</span>
            </div>
            <div class="progress-track" style="margin-bottom:0.875rem;"><div class="progress-fill {{ $goal->status==='completed'?'green':'' }}" style="width:{{ min(100,$pct) }}%;"></div></div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.875rem;">📅 Due {{ \Carbon\Carbon::parse($goal->target_date)->format('d M Y') }}</div>
            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                <form method="POST" action="{{ route('faculty.goals.update', $goal->id) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="completion_percentage" value="{{ min(100, $pct + 25) }}">
                    <input type="hidden" name="status" value="{{ ($pct+25)>=100?'completed':'active' }}">
                    <button class="btn btn-ghost btn-xs" {{ $pct>=100?'disabled':'' }}>+25%</button>
                </form>
                <form method="POST" action="{{ route('faculty.goals.destroy', $goal->id) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-xs">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="glass-card" style="grid-column:1/-1; text-align:center; padding:3rem;">
            <div style="font-size:2.5rem; margin-bottom:0.75rem;">🎯</div>
            <div style="color:var(--text-muted);">No goals yet. Set your first professional milestone!</div>
        </div>
        @endforelse
    </div>

    {{-- CREATE MODAL --}}
    <div id="createGoalModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-title">🎯 New Goal</div>
            <form method="POST" action="{{ route('faculty.goals.store') }}">
                @csrf
                <div style="display:flex; flex-direction:column; gap:0.875rem; margin-bottom:1.25rem;">
                    <div class="form-group"><label>Title</label><input name="title" placeholder="e.g. Complete Data Science Certification" required></div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="2" placeholder="Details…" required></textarea></div>
                    <div class="form-group"><label>Target Date</label><input type="date" name="target_date" required></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('createGoalModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
