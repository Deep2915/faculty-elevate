<x-app-layout>
    <x-slot name="header">HOD Dashboard</x-slot>

    {{-- KPI Row --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-bottom:1.75rem;">
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(99,102,241,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div><div class="kpi-value">{{ $totalFaculty }}</div><div class="kpi-label">Faculty Members</div></div>
            </div>
        </div>
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(245,158,11,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div><div class="kpi-value">{{ $pendingEvals }}</div><div class="kpi-label">Pending Drafts</div></div>
            </div>
        </div>
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(16,185,129,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div><div class="kpi-value">{{ $publishedEvals }}</div><div class="kpi-label">Published Evals</div></div>
            </div>
        </div>
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(6,182,212,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#06b6d4" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                </div>
                <div><div class="kpi-value">{{ number_format($avgPI * 100, 1) }}%</div><div class="kpi-label">Dept Avg PI</div></div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div style="display:grid; grid-template-columns:1.4fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
        {{-- Performance Chart --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Faculty Performance Index</span>
                <a href="{{ route('hod.evaluations.index') }}" class="btn btn-ghost btn-xs">Evaluate</a>
            </div>
            <div class="chart-container" style="height:260px;">
                <canvas id="piBar"></canvas>
            </div>
        </div>

        {{-- Burnout Alerts --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Burnout Alerts</span>
                <span class="pill pill-rose">Wellbeing</span>
            </div>
            @forelse($burnoutAlerts as $alert)
            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.625rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                <div class="avatar" style="width:32px;height:32px;font-size:0.75rem;background:linear-gradient(135deg,#f43f5e,#fb923c);">
                    {{ strtoupper(substr($alert->faculty->name,0,1)) }}
                </div>
                <div style="flex:1;">
                    <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary);">{{ $alert->faculty->name }}</div>
                    <div style="font-size:0.7rem; color:var(--text-muted);">Burnout Index: {{ number_format($alert->burnout_index, 1) }}%</div>
                </div>
                <span class="pill pill-rose">High Risk</span>
            </div>
            @empty
            <div style="text-align:center; padding:2rem; color:var(--text-muted); font-size:0.875rem;">
                All clear — department is healthy!
            </div>
            @endforelse
        </div>
    </div>

    {{-- Top Performers Table --}}
    <div class="glass-card">
        <div class="section-header">
            <span class="section-title">Department Leaderboard</span>
            <a href="{{ route('hod.leaderboard') }}" class="btn btn-ghost btn-xs">Full View →</a>
        </div>
        <table class="data-table">
            <thead><tr>
                <th style="padding-left:1.5rem;">Rank</th>
                <th>Faculty</th>
                <th>Performance Index</th>
                <th>XP</th>
                <th>Level</th>
                <th style="padding-right:1.5rem; text-align:right;">Report</th>
            </tr></thead>
            <tbody>
                @foreach($topFaculty as $i => $p)
                <tr>
                    <td style="padding-left:1.5rem;">
                        @if($i===0)<span class="rank-1">1st</span>
                        @elseif($i===1)<span class="rank-2">2nd</span>
                        @elseif($i===2)<span class="rank-3">3rd</span>
                        @else<span style="color:var(--text-muted);">{{ $i+1 }}</span>@endif
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.625rem;">
                            <div class="avatar" style="width:30px;height:30px;font-size:0.72rem;">{{ strtoupper(substr($p->user->name,0,1)) }}</div>
                            <span style="font-weight:600; color:var(--text-primary);">{{ $p->user->name }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.625rem;">
                            <div class="progress-track" style="width:120px;">
                                <div class="progress-fill {{ ($p->performance_index??0)>=0.8?'green':(($p->performance_index??0)>=0.5?'':'rose') }}" style="width:{{ min(100,($p->performance_index??0)*100) }}%"></div>
                            </div>
                            <span style="font-size:0.8rem; font-weight:600; color:var(--brand-400);">{{ number_format(($p->performance_index??0)*100,1) }}%</span>
                        </div>
                    </td>
                    <td><span class="pill pill-amber">{{ number_format($p->xp??0) }} XP</span></td>
                    <td><span class="pill pill-indigo">Lv {{ $p->level??1 }}</span></td>
                    <td style="text-align:right; padding-right:1.5rem;">
                        <a href="{{ route('hod.report.download', $p->user->id) }}" class="btn btn-ghost btn-xs">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            PDF
                        </a>
                    </td>
                </tr>
                @endforeach
                @if($topFaculty->isEmpty())
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-muted);">No evaluated faculty yet.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    Chart.defaults.color = '#9ca3c8';
    Chart.defaults.borderColor = 'rgba(129,140,248,0.1)';
    @php
        $chartNames = $piChartData->map(function($p){ return $p->user->name; })->values();
        $chartScores= $piChartData->map(function($p){ return round(($p->performance_index??0)*100,1); })->values();
    @endphp
    new Chart(document.getElementById('piBar'), {
        type: 'bar',
        data: {
            labels: {{ json_encode($chartNames) }},
            datasets: [{
                label: 'Performance Index %',
                data: {{ json_encode($chartScores) }},
                backgroundColor: 'rgba(99,102,241,0.65)',
                borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { min: 0, max: 100, grid: { color: 'rgba(255,255,255,0.04)' } }
            }
        }
    });
    </script>
    @endpush
</x-app-layout>
