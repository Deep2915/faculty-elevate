<x-app-layout>
    <x-slot name="header">My Dashboard</x-slot>

    @php
        $pi  = (float)($profile->performance_index ?? 0);
        $xp  = (int)($profile->xp ?? 0);
        $lv  = (int)($profile->level ?? 1);
        $xpNext = $lv * 500;
        $xpPct   = min(100, ($xp % 500) / 500 * 100);
        $piPct   = min(100, $pi * 100);
    @endphp

    {{-- Hero KPI Row --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.75rem;">
        {{-- PI Score --}}
        <div class="kpi-card" style="border-color:rgba(99,102,241,0.35);">
            <div class="kpi-label">Performance Index</div>
            <div style="display:flex; align-items:flex-end; gap:0.5rem;">
                <div class="kpi-value gradient-text">{{ number_format($piPct,1) }}%</div>
            </div>
            <div class="progress-track"><div class="progress-fill {{ $piPct>=80?'green':($piPct>=50?'':'rose') }}" style="width:{{ $piPct }}%;"></div></div>
        </div>

        {{-- XP --}}
        <div class="kpi-card">
            <div class="kpi-label">Total XP</div>
            <div class="kpi-value" style="color:var(--accent-amber);">{{ number_format($xp) }}</div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Level {{ $lv }} — {{ number_format($xpNext - ($xp % 500)) }} XP to next</div>
            <div class="progress-track"><div class="progress-fill amber" style="width:{{ $xpPct }}%;"></div></div>
        </div>

        {{-- Goals --}}
        <div class="kpi-card">
            <div class="kpi-label">Goals Progress</div>
            <div class="kpi-value">{{ $goalsCompleted }}<span style="font-size:1.1rem; color:var(--text-muted);"> / {{ $goalsTotal }}</span></div>
            @if($goalsTotal > 0)
            <div class="progress-track"><div class="progress-fill green" style="width:{{ round($goalsCompleted/$goalsTotal*100) }}%;"></div></div>
            @endif
        </div>

        {{-- Research --}}
        <div class="kpi-card">
            <div class="kpi-label">Research Score</div>
            <div class="kpi-value" style="color:var(--accent-cyan);">{{ number_format(($profile->research_score??0)*100,0) }}%</div>
            <div class="progress-track"><div class="progress-fill" style="background:linear-gradient(90deg,#06b6d4,#38bdf8);width:{{ min(100,($profile->research_score??0)*100) }}%;"></div></div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div style="display:grid; grid-template-columns:1.35fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
        {{-- Evaluation History Chart --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Evaluation History</span>
                <span class="pill pill-indigo">Published</span>
            </div>
            @if($evaluations->isNotEmpty())
            <div class="chart-container" style="height:230px;"><canvas id="evalChart"></canvas></div>
            @else
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">No published evaluations yet.</div>
            @endif
        </div>

        {{-- Recommendations --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Recommendations</span>
                <span class="pill pill-cyan">Smart Engine</span>
            </div>
            @if(!empty($recommendations['workshops']))
                <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.5rem; text-transform:uppercase; letter-spacing:0.08em;">Suggested Workshops</div>
                @foreach($recommendations['workshops'] as $ws)
                <div style="display:flex; align-items:center; gap:0.75rem; padding:0.625rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary);">{{ $ws->title }}</div>
                        <div style="font-size:0.72rem; color:var(--text-muted);">{{ ucfirst($ws->category) }} · +{{ $ws->xp_reward }} XP</div>
                    </div>
                    <a href="{{ route('faculty.workshops.index') }}" class="btn btn-ghost btn-xs" style="margin-left:auto;">Register</a>
                </div>
                @endforeach
            @endif
            @if(!empty($recommendations['certifications']))
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.875rem; margin-bottom:0.5rem; text-transform:uppercase; letter-spacing:0.08em;">Suggested Certifications</div>
                @foreach($recommendations['certifications'] as $cert)
                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem 0;">
                    <span style="color:var(--accent-amber); font-weight:700; font-size:.85rem;">&#10022;</span>
                    <span style="font-size:0.8125rem; color:var(--text-secondary);">{{ $cert }}</span>
                </div>
                @endforeach
            @endif
            @if(!empty($recommendations['skill_gaps']))
                @foreach($recommendations['skill_gaps'] as $gap)
                <div style="background:rgba(244,63,94,0.08); border:1px solid rgba(244,63,94,0.2); border-radius:8px; padding:0.625rem 0.875rem; font-size:0.8125rem; color:#fda4af; margin-top:0.5rem;">Note: {{ $gap }}</div>
                @endforeach
            @endif
            @if(empty($recommendations['workshops']) && empty($recommendations['certifications']) && empty($recommendations['skill_gaps']))
            <div style="text-align:center; padding:2rem; color:var(--text-muted);">Great work! No immediate recommendations.</div>
            @endif
        </div>
    </div>

    {{-- Timeline + Wellbeing --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
        {{-- Activity Timeline --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Recent Activity</span>
                <a href="{{ route('faculty.achievements.index') }}" class="btn btn-ghost btn-xs">View All</a>
            </div>
            @forelse($achievements as $ach)
            <div class="timeline-item">
                <div class="timeline-dot" style="background:rgba(99,102,241,0.2);color:var(--brand-400);">
                    {{ ['publication'=>'PUB','patent'=>'PAT','award'=>'AWD','certification'=>'CERT'][$ach->type] ?? '&#9733;' }}
                </div>
                <div class="timeline-body">
                    <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary);">{{ $ach->title }}</div>
                    <div class="timeline-text">{{ $ach->journal_or_body }}</div>
                    <div class="timeline-time">{{ \Carbon\Carbon::parse($ach->date)->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:2rem; color:var(--text-muted);">No achievements yet. <a href="{{ route('faculty.achievements.index') }}" style="color:var(--brand-400);">Add one →</a></div>
            @endforelse
        </div>

        {{-- Wellbeing Trend --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Wellbeing Trend</span>
                <a href="{{ route('faculty.wellbeing') }}" class="btn btn-primary btn-xs">Check In</a>
            </div>
            @if($wellbeingData->isNotEmpty())
            <div class="chart-container" style="height:200px;"><canvas id="wellbeingChart"></canvas></div>
            @else
            <div style="text-align:center; padding:2rem; color:var(--text-muted);">No wellbeing data. Take your first check-in!</div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    Chart.defaults.color = '#9ca3c8';
    Chart.defaults.borderColor = 'rgba(129,140,248,0.1)';

    @if($evaluations->isNotEmpty())
    @php
        $evalsSorted  = $evaluations->sortBy('created_at')->values();
        $evalLabels   = $evalsSorted->pluck('period');
        $evalResearch = $evalsSorted->map(fn($e) => round((float)data_get($e->scores, 'research', 0) * 100, 1));
        $evalTeaching = $evalsSorted->map(fn($e) => round((float)data_get($e->scores, 'teaching', 0) * 100, 1));
        $evalInnov    = $evalsSorted->map(fn($e) => round((float)data_get($e->scores, 'innovation', 0) * 100, 1));
    @endphp
    new Chart(document.getElementById('evalChart'), {
        type: 'line',
        data: {
            labels: @json($evalLabels),
            datasets: [
                {
                    label: 'Research',
                    data: @json($evalResearch),
                    borderColor:'#818cf8', backgroundColor:'rgba(129,140,248,0.08)',
                    tension:0.4, fill:true, pointRadius:5, pointBackgroundColor:'#818cf8',
                    pointHoverRadius:7,
                },
                {
                    label: 'Teaching',
                    data: @json($evalTeaching),
                    borderColor:'#10b981', backgroundColor:'rgba(16,185,129,0.08)',
                    tension:0.4, fill:true, pointRadius:5, pointBackgroundColor:'#10b981',
                    pointHoverRadius:7,
                },
                {
                    label: 'Innovation',
                    data: @json($evalInnov),
                    borderColor:'#f59e0b', backgroundColor:'rgba(245,158,11,0.08)',
                    tension:0.4, fill:true, pointRadius:5, pointBackgroundColor:'#f59e0b',
                    pointHoverRadius:7,
                },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14, font: { size: 11 } } },
                tooltip: {
                    callbacks: { label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + '%' }
                }
            },
            scales: {
                y: { min: 0, max: 100, ticks: { callback: v => v + '%', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });
    @endif

    @if($wellbeingData->isNotEmpty())
    @php
        $wbLabels = $wellbeingData->map(function($s) {
            $dt = $s->surveyed_at;
            if ($dt instanceof \Carbon\Carbon) return $dt->format('d M');
            if (is_array($dt) && isset($dt['$date'])) return \Carbon\Carbon::parse($dt['$date'])->format('d M');
            if (is_string($dt)) return \Carbon\Carbon::parse($dt)->format('d M');
            return '—';
        });
        $wbData = $wellbeingData->map(fn($s) => max(0, min(100, (float)($s->burnout_index ?? 0))));
    @endphp
    new Chart(document.getElementById('wellbeingChart'), {
        type: 'line',
        data: {
            labels: @json($wbLabels),
            datasets: [{
                label: 'Wellbeing Score',
                data: @json($wbData),
                borderColor: '#f43f5e',
                backgroundColor: 'rgba(244,63,94,0.1)',
                tension: 0.4, fill: true,
                pointRadius: 5, pointBackgroundColor:'#f43f5e', pointHoverRadius:7,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' Score: ' + ctx.parsed.y } }
            },
            scales: {
                y: { min: 0, max: 100, ticks: { font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });
    @endif
    </script>
    @endpush
</x-app-layout>
