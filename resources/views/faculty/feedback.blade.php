<x-app-layout>
    <x-slot name="header">My Student Feedback</x-slot>

    @push('styles')
    <style>
    .fb-dims { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; margin-bottom:1.5rem; }
    .dim-kpi { background:var(--surface-700); border-radius:12px; padding:1rem 1.25rem; border:1px solid var(--glass-border); text-align:center; transition:transform .2s; }
    .dim-kpi:hover { transform:translateY(-2px); }
    .dim-kpi-val { font-size:1.75rem; font-weight:800; }
    .dim-kpi-label { font-size:0.72rem; color:var(--text-muted); margin-top:4px; }
    .comment-card { background:var(--surface-700); border-left:3px solid var(--brand-500); border-radius:0 10px 10px 0; padding:0.875rem 1rem; margin-bottom:0.75rem; font-size:0.85rem; color:var(--text-secondary); line-height:1.6; position:relative; }
    .comment-card .anon { font-size:0.68rem; color:var(--text-muted); margin-top:4px; }
    .no-feedback { text-align:center; padding:3rem; color:var(--text-muted); }
    </style>
    @endpush

    @php
        $count   = $scores['count'] ?? 0;
        $dims    = ['clarity' => ['label'=>'Clarity','icon'=>'C'], 'communication' => ['label'=>'Communication','icon'=>'CM'], 'punctuality' => ['label'=>'Punctuality','icon'=>'P'], 'engagement' => ['label'=>'Engagement','icon'=>'E']];
        $overall = $count > 0 ? round(collect(['clarity','communication','punctuality','engagement'])->avg(fn($d) => $scores[$d] ?? 0) * 100, 1) : 0;
        $radarData = [round(($scores['clarity']??0)*100,1), round(($scores['communication']??0)*100,1), round(($scores['punctuality']??0)*100,1), round(($scores['engagement']??0)*100,1)];
    @endphp

    @if($count === 0)
    <div class="glass-card no-feedback">
        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem; display:block; color:var(--text-muted);"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <h3 style="font-weight:600; color:var(--text-secondary); margin-bottom:0.5rem;">No Feedback Yet</h3>
        <p style="font-size:0.85rem;">Ask your HOD to generate a feedback link and share it with your students.</p>
        @if($feedbackLink)
        <div style="margin-top:1rem; font-size:0.8rem; color:var(--text-muted);">Your current link: <span style="color:var(--brand-400);">{{ $feedbackLink }}</span></div>
        @endif
    </div>
    @else
    <div style="display:grid; grid-template-columns:1fr 1.5fr; gap:1.5rem; align-items:start;">

        {{-- Left: Radar + KPIs --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Overall Badge --}}
            <div class="glass-card" style="text-align:center; padding:1.75rem;">
                <div style="font-size:2.5rem; font-weight:800; color:{{ $overall >= 75 ? 'var(--accent-green)' : ($overall >= 50 ? 'var(--accent-amber)' : 'var(--accent-rose)') }};">{{ $overall }}%</div>
                <div style="font-size:0.8rem; color:var(--text-muted); margin:4px 0 12px;">Overall Score</div>
                <span class="pill {{ $overall >= 75 ? 'pill-green' : ($overall >= 50 ? 'pill-amber' : 'pill-rose') }}">
                    Based on {{ $count }} submission{{ $count !== 1 ? 's' : '' }}
                </span>
            </div>

            {{-- Radar Chart --}}
            <div class="glass-card">
                <div class="section-title" style="margin-bottom:1rem;">Feedback Radar</div>
                <div class="chart-container" style="height:220px;">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>

            {{-- Dimension KPIs --}}
            <div class="fb-dims">
            @foreach($dims as $key => $d)
            @php $val = round(($scores[$key] ?? 0)*100, 1); @endphp
            <div class="dim-kpi">
                <div style="font-size:1.25rem; margin-bottom:4px;">{{ $d['icon'] }}</div>
                <div class="dim-kpi-val" style="color:{{ $val >= 75 ? 'var(--accent-green)' : ($val >= 50 ? 'var(--accent-amber)' : 'var(--accent-rose)') }};">{{ $val }}%</div>
                <div class="dim-kpi-label">{{ $d['label'] }}</div>
            </div>
            @endforeach
            </div>
        </div>

        {{-- Right: Comments --}}
        <div class="glass-card">
            <div class="section-header">
                <div class="section-title">Student Comments</div>
                <span class="pill pill-indigo">Anonymous</span>
            </div>
            @forelse($recentFeedbacks as $fb)
            <div class="comment-card">
                {{ $fb->comment }}
                <div class="anon">Anonymous · {{ \Carbon\Carbon::parse($fb->submitted_at)->diffForHumans() }}</div>
            </div>
            @empty
            <div style="padding:2rem; text-align:center; color:var(--text-muted); font-size:0.85rem;">No written comments yet.</div>
            @endforelse

            @if($feedbackLink)
            <div style="margin-top:1.25rem; padding-top:1rem; border-top:1px solid var(--glass-border);">
                <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.5rem;">Your shareable feedback link</div>
                <div style="background:var(--surface-700); border-radius:8px; padding:8px 12px; font-size:0.75rem; color:var(--brand-400); word-break:break-all;">{{ $feedbackLink }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    const radarCtx = document.getElementById('radarChart');
    if (radarCtx) {
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: ['Clarity', 'Communication', 'Punctuality', 'Engagement'],
                datasets: [{
                    label: 'Your Score',
                    data: @json($radarData),
                    backgroundColor: 'rgba(99,102,241,0.2)',
                    borderColor: '#6366f1',
                    borderWidth: 2,
                    pointBackgroundColor: '#818cf8',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        min: 0, max: 100,
                        ticks: { color: '#5b6184', font: { size: 10 }, stepSize: 25 },
                        grid: { color: 'rgba(255,255,255,0.06)' },
                        pointLabels: { color: '#9ca3c8', font: { size: 11 } },
                        angleLines: { color: 'rgba(255,255,255,0.06)' },
                    }
                }
            }
        });
    }
    </script>
    @endpush
</x-app-layout>
