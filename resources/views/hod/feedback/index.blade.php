<x-app-layout>
    <x-slot name="header">Student Feedback Overview</x-slot>

    @push('styles')
    <style>
    .fb-card { background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:16px; padding:1.5rem; transition:all .25s; position:relative; overflow:hidden; }
    .fb-card::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg, rgba(99,102,241,0.05), transparent); opacity:0; transition:opacity .25s; }
    .fb-card:hover { border-color:rgba(129,140,248,0.4); transform:translateY(-2px); box-shadow:0 12px 40px rgba(0,0,0,.35); }
    .fb-card:hover::before { opacity:1; }
    .fb-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:1.25rem; }
    .dim-bar { display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem; }
    .dim-label { font-size:0.75rem; color:var(--text-muted); width:100px; flex-shrink:0; }
    .star-avg { font-size:1.2rem; font-weight:700; }
    .stagger-in { opacity:0; animation:staggerFade .5s ease forwards; }
    @keyframes staggerFade { to { opacity:1; transform:translateY(0); } from { opacity:0; transform:translateY(14px); } }
    .fb-card.highlight { border-color:rgba(129,140,248,0.7); box-shadow:0 0 0 3px rgba(99,102,241,0.25), 0 12px 40px rgba(0,0,0,.35); animation:cardPulse 1.5s ease 0.5s 2; }
    @keyframes cardPulse { 0%,100% { box-shadow:0 0 0 3px rgba(99,102,241,0.25), 0 12px 40px rgba(0,0,0,.35); } 50% { box-shadow:0 0 0 6px rgba(99,102,241,0.4), 0 16px 48px rgba(0,0,0,.45); } }
    </style>
    @endpush

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:1rem; font-weight:600; color:var(--text-primary);">Anonymous Student Feedback</h2>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Aggregated scores from anonymous student submissions across all faculty members.</p>
        </div>
    </div>

    <div class="fb-grid">
    @foreach($feedbackStats as $i => $stat)
    @php
        $f       = $stat['faculty'];
        $scores  = $stat['scores'];
        $count   = $scores['count'] ?? 0;
        $dims    = ['clarity' => 'Clarity', 'communication' => 'Communication', 'punctuality' => 'Punctuality', 'engagement' => 'Engagement'];
        $overall = $count > 0 ? round(collect(['clarity','communication','punctuality','engagement'])->avg(fn($d) => $scores[$d] ?? 0) * 100, 1) : null;
        $pillCls = $overall !== null ? ($overall >= 75 ? 'pill-green' : ($overall >= 50 ? 'pill-amber' : 'pill-rose')) : 'pill-gray';
    @endphp
    <div class="fb-card stagger-in" id="faculty-{{ $f->id }}" style="animation-delay:{{ $i * 0.06 }}s; scroll-margin-top: 5rem;">
        {{-- Header --}}
        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
            <div class="avatar avatar-lg">{{ strtoupper(substr($f->name, 0, 1)) }}</div>
            <div style="flex:1;">
                <a href="{{ route('hod.evaluations.index') }}?faculty={{ $f->id }}" style="font-weight:700; color:var(--text-primary); text-decoration:none; transition:color .15s;" onmouseover="this.style.color='var(--brand-400)'" onmouseout="this.style.color='var(--text-primary)'">{{ $f->name }}</a>
                <div style="font-size:0.75rem; color:var(--text-muted);">{{ $f->email }}</div>
            </div>
            @if($overall !== null)
                <span class="pill {{ $pillCls }}" style="font-size:0.8rem; padding:0.25rem 0.75rem;">{{ $overall }}%</span>
            @else
                <span class="pill pill-gray">No data</span>
            @endif
        </div>

        {{-- Score Bars --}}
        @if($count > 0)
        <div style="margin-bottom:1.25rem;">
            @foreach($dims as $key => $label)
            @php $val = (float)($scores[$key] ?? 0); $valPct = round($val * 100); @endphp
            <div class="dim-bar">
                <span class="dim-label">{{ $label }}</span>
                <div class="progress-track" style="flex:1;">
                    <div class="progress-fill {{ $valPct >= 75 ? 'green' : ($valPct >= 50 ? '' : 'rose') }}" style="width:{{ $valPct }}%"></div>
                </div>
                <span style="font-size:0.75rem; font-weight:600; color:var(--text-secondary); width:36px; text-align:right;">{{ $valPct }}%</span>
            </div>
            @endforeach
            <div style="font-size:0.73rem; color:var(--text-muted); margin-top:0.5rem;">{{ $count }} student submission{{ $count !== 1 ? 's' : '' }}</div>
        </div>
        @else
        <div style="padding:1rem 0; color:var(--text-muted); font-size:0.83rem;">No feedback submitted yet.</div>
        @endif

        {{-- Submission Count Footer --}}
        <div style="border-top:1px solid var(--glass-border); padding-top:0.875rem; margin-top:auto; display:flex; align-items:center; gap:0.5rem;">
            <svg width="13" height="13" fill="none" stroke="var(--text-muted)" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span style="font-size:0.75rem; color:var(--text-muted);">
                {{ $count }} anonymous student submission{{ $count !== 1 ? 's' : '' }} collected
            </span>
        </div>
    </div>
    @endforeach
    </div>

    @push('scripts')
    <script>
    // Highlight the card targeted via URL hash (e.g. #faculty-123)
    window.addEventListener('load', function () {
        const hash = window.location.hash;
        if (hash && hash.startsWith('#faculty-')) {
            const card = document.querySelector(hash);
            if (card) {
                setTimeout(() => {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    card.classList.add('highlight');
                }, 400);
            }
        }
    });
    </script>
    @endpush
</x-app-layout>
