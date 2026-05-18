<x-app-layout>
    <x-slot name="header">Achievements</x-slot>

    <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem;">
        <button onclick="document.getElementById('createAchModal').style.display='flex'" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Achievement
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('faculty.achievements.index') }}" class="glass-card" style="margin-bottom:1.25rem; padding:1rem 1.25rem;">
        <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:0.75rem; align-items:end;">
            <div class="form-group"><label>Search</label><input name="q" value="{{ request('q') }}" placeholder="Title or journal…"></div>
            <div class="form-group"><label>Type</label>
                <select name="type">
                    <option value="">All Types</option>
                    @foreach(['publication','patent','award','certification'] as $t)
                        <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Sort</label>
                <select name="sort">
                    <option value="date" @selected(request('sort','date')==='date')>Date</option>
                    <option value="xp_awarded" @selected(request('sort')==='xp_awarded')>XP</option>
                    <option value="title" @selected(request('sort')==='title')>Title</option>
                </select>
            </div>
            <div class="form-group"><label>Direction</label>
                <select name="direction">
                    <option value="desc" @selected(request('direction','desc')==='desc')>Newest</option>
                    <option value="asc" @selected(request('direction')==='asc')>Oldest</option>
                </select>
            </div>
            <button class="btn btn-ghost" type="submit" style="align-self:flex-end;">Filter</button>
        </div>
    </form>

    {{-- Timeline Feed --}}
    <div class="glass-card">
        @forelse($achievements as $ach)
        @php
            $icons = ['publication'=>['icon'=>'📄','color'=>'rgba(99,102,241,0.2)','pill'=>'pill-indigo'],
                      'patent'     =>['icon'=>'💡','color'=>'rgba(245,158,11,0.2)','pill'=>'pill-amber'],
                      'award'      =>['icon'=>'🏆','color'=>'rgba(16,185,129,0.2)','pill'=>'pill-green'],
                      'certification'=>['icon'=>'🎓','color'=>'rgba(6,182,212,0.2)','pill'=>'pill-cyan']];
            $meta = $icons[$ach->type] ?? ['icon'=>'✨','color'=>'rgba(99,102,241,0.2)','pill'=>'pill-gray'];
        @endphp
        <div style="display:flex; gap:1.25rem; padding:1rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
            <div style="width:48px; height:48px; border-radius:14px; background:{{ $meta['color'] }}; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0;">{{ $meta['icon'] }}</div>
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:0.625rem; flex-wrap:wrap; margin-bottom:0.25rem;">
                    <span style="font-weight:700; color:var(--text-primary); font-size:0.9375rem;">{{ $ach->title }}</span>
                    <span class="pill {{ $meta['pill'] }}">{{ ucfirst($ach->type) }}</span>
                    @if($ach->verified ?? false)<span class="pill pill-green">✓ Verified</span>@endif
                </div>
                <div style="font-size:0.8125rem; color:var(--text-secondary);">{{ $ach->journal_or_body }}</div>
                <div style="font-size:0.72rem; color:var(--text-muted); margin-top:0.25rem;">{{ \Carbon\Carbon::parse($ach->date)->format('d M Y') }}</div>
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:0.5rem; flex-shrink:0;">
                <span class="pill pill-amber">+{{ $ach->xp_awarded }} XP</span>
                @if($ach->proof_url)
                <a href="{{ $ach->proof_url }}" target="_blank" class="btn btn-ghost btn-xs">Proof ↗</a>
                @endif
                <form method="POST" action="{{ route('faculty.achievements.destroy', $ach->id) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-xs">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:3rem; color:var(--text-muted);">
            <div style="font-size:2.5rem; margin-bottom:0.75rem;">🏅</div>
            No achievements logged yet. Add your publications, patents, or awards!
        </div>
        @endforelse
        <div style="padding-top:1rem;">{{ $achievements->links() }}</div>
    </div>

    {{-- CREATE MODAL --}}
    <div id="createAchModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-title">🏅 Log Achievement</div>
            <form method="POST" action="{{ route('faculty.achievements.store') }}">
                @csrf
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group"><label>Type</label>
                        <select name="type" required>
                            <option value="publication">📄 Publication</option>
                            <option value="patent">💡 Patent</option>
                            <option value="award">🏆 Award</option>
                            <option value="certification">🎓 Certification</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Date</label><input type="date" name="date" required></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Title</label><input name="title" placeholder="e.g. Deep Learning for Medical Imaging" required></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Journal / Awarding Body</label><input name="journal_or_body" placeholder="e.g. IEEE Transactions, Google Inc." required></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Proof URL (optional)</label><input type="url" name="proof_url" placeholder="https://…"></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('createAchModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button class="btn btn-primary">Log Achievement</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
