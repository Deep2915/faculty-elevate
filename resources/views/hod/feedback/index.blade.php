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
    .copy-btn { cursor:pointer; background:var(--surface-700); border:1px solid var(--glass-border); border-radius:8px; padding:4px 10px; font-size:0.72rem; color:var(--text-secondary); transition:all .18s; }
    .copy-btn:hover { background:var(--brand-500); color:#fff; border-color:var(--brand-500); }
    .link-box { background:var(--surface-700); border:1px dashed var(--glass-border); border-radius:8px; padding:8px 12px; font-size:0.75rem; color:var(--text-muted); word-break:break-all; margin-top:0.5rem; }
    .no-link { font-size:0.78rem; color:var(--text-muted); font-style:italic; }
    .stagger-in { opacity:0; animation:staggerFade .5s ease forwards; }
    @keyframes staggerFade { to { opacity:1; transform:translateY(0); } from { opacity:0; transform:translateY(14px); } }
    </style>
    @endpush

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:1rem; font-weight:600; color:var(--text-primary);">Anonymous Student Feedback</h2>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Generate unique links for each faculty. Share with students — no login required.</p>
        </div>
    </div>

    <div class="fb-grid">
    @foreach($feedbackStats as $i => $stat)
    @php
        $f       = $stat['faculty'];
        $scores  = $stat['scores'];
        $link    = $stat['link'];
        $count   = $scores['count'] ?? 0;
        $dims    = ['clarity' => 'Clarity', 'communication' => 'Communication', 'punctuality' => 'Punctuality', 'engagement' => 'Engagement'];
        $overall = $count > 0 ? round(collect(['clarity','communication','punctuality','engagement'])->avg(fn($d) => $scores[$d] ?? 0) * 100, 1) : null;
        $pillCls = $overall !== null ? ($overall >= 75 ? 'pill-green' : ($overall >= 50 ? 'pill-amber' : 'pill-rose')) : 'pill-gray';
    @endphp
    <div class="fb-card stagger-in" style="animation-delay:{{ $i * 0.06 }}s;">
        {{-- Header --}}
        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
            <div class="avatar avatar-lg">{{ strtoupper(substr($f->name, 0, 1)) }}</div>
            <div style="flex:1;">
                <div style="font-weight:700; color:var(--text-primary);">{{ $f->name }}</div>
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

        {{-- Link Section --}}
        <div style="border-top:1px solid var(--glass-border); padding-top:1rem; margin-top:auto;">
            @if($link)
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                    <span style="font-size:0.75rem; color:var(--text-muted);">Feedback Link</span>
                    <button class="copy-btn" onclick="copyLink('link-{{ $f->id }}')">Copy Link</button>
                </div>
                <div class="link-box" id="link-{{ $f->id }}">{{ $link }}</div>
            @else
                <div class="no-link" style="margin-bottom:0.75rem;">No feedback link yet. Generate one to share with students.</div>
            @endif
            <form method="POST" action="{{ route('hod.feedback.generate-link', $f->id) }}" style="margin-top:0.75rem;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" style="width:100%; justify-content:center;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    {{ $link ? 'Regenerate Link' : 'Generate Link' }}
                </button>
            </form>
        </div>
    </div>
    @endforeach
    </div>

    @push('scripts')
    <script>
    function copyLink(id) {
        const el = document.getElementById(id);
        if (!el) return;
        navigator.clipboard.writeText(el.textContent.trim()).then(() => {
            const btn = event.target;
            btn.textContent = '✓ Copied!';
            setTimeout(() => btn.textContent = 'Copy Link', 2000);
        });
    }
    </script>
    @endpush
</x-app-layout>
