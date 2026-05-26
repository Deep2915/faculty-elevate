<x-app-layout>
    <x-slot name="header">My Dashboard</x-slot>

    @php
        $pi     = (float)($profile->performance_index ?? 0);
        $xp     = (int)($profile->xp ?? 0);
        $lv     = (int)($profile->level ?? 1);
        $xpNext = $lv * 500;
        $xpPct  = min(100, ($xp % 500) / 500 * 100);
        $piPct  = min(100, $pi * 100);
        $attPct = min(100, ($attendanceData['score'] ?? 0) * 100);
        $attColor = $attPct >= 85 ? 'green' : ($attPct >= 70 ? 'amber' : 'rose');
    @endphp

    {{-- ── Hero KPI Row ──────────────────────────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(175px,1fr)); gap:1rem; margin-bottom:1.75rem;">
        {{-- PI Score --}}
        <div class="kpi-card" style="border-color:rgba(99,102,241,0.35);">
            <div class="kpi-label">Performance Index</div>
            <div style="display:flex; align-items:flex-end; gap:0.5rem;">
                <div class="kpi-value gradient-text">{{ number_format($piPct,1) }}%</div>
            </div>
            <div class="progress-track"><div class="progress-fill {{ $piPct>=80?'green':($piPct>=50?'':'rose') }}" style="width:{{ $piPct }}%;"></div></div>
        </div>

        {{-- XP / Level --}}
        <div class="kpi-card">
            <div class="kpi-label">Total XP · Level {{ $lv }}</div>
            <div class="kpi-value" style="color:var(--accent-amber);">{{ number_format($xp) }}</div>
            <div style="font-size:0.72rem; color:var(--text-muted); margin-bottom:4px;">{{ number_format($xpNext - ($xp % 500)) }} XP to Level {{ $lv + 1 }}</div>
            <div class="progress-track"><div class="progress-fill amber" style="width:{{ $xpPct }}%;"></div></div>
        </div>

        {{-- Goals --}}
        <div class="kpi-card">
            <div class="kpi-label">Goals</div>
            <div class="kpi-value">{{ $goalsCompleted }}<span style="font-size:1rem; color:var(--text-muted);"> / {{ $goalsTotal }}</span></div>
            <div style="font-size:0.72rem; color:var(--text-muted); margin-bottom:4px;">{{ $goalsActive }} active · {{ $goalsCompleted }} done</div>
            @if($goalsTotal > 0)
            <div class="progress-track"><div class="progress-fill green" style="width:{{ round($goalsCompleted/$goalsTotal*100) }}%;"></div></div>
            @endif
        </div>

        {{-- Attendance --}}
        <div class="kpi-card">
            <div class="kpi-label">Class Attendance</div>
            <div class="kpi-value" style="color:var(--accent-{{ $attColor === 'green' ? 'cyan' : ($attColor === 'amber' ? 'amber' : 'rose') }});">{{ number_format($attPct, 1) }}%</div>
            <div style="font-size:0.72rem; color:var(--text-muted); margin-bottom:4px;">{{ $attendanceData['conducted'] ?? 0 }} / {{ $attendanceData['total'] ?? 0 }} classes</div>
            <div class="progress-track"><div class="progress-fill {{ $attColor }}" style="width:{{ $attPct }}%;"></div></div>
        </div>

        {{-- Research Score --}}
        <div class="kpi-card">
            <div class="kpi-label">Research Score</div>
            <div class="kpi-value" style="color:var(--accent-cyan);">{{ number_format(($profile->research_score??0)*100,0) }}%</div>
            <div class="progress-track"><div class="progress-fill" style="background:linear-gradient(90deg,#06b6d4,#38bdf8);width:{{ min(100,($profile->research_score??0)*100) }}%;"></div></div>
        </div>
    </div>

    {{-- ── Today's Classes + Evaluation History ────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:1fr 1.4fr; gap:1.5rem; margin-bottom:1.5rem;">

        {{-- Today's Schedule --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Today's Classes</span>
                <span style="font-size:0.72rem; color:var(--text-muted);">{{ $todayName }}</span>
                <a href="{{ route('faculty.timetable') }}" class="btn btn-ghost btn-xs">Full Timetable →</a>
            </div>
            @forelse($todayClasses as $cls)
            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.625rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                <div style="width:56px; flex-shrink:0; text-align:center; background:rgba(99,102,241,0.12); border-radius:8px; padding:4px 0; font-size:0.68rem; font-weight:700; color:var(--brand-400); line-height:1.3;">
                    {{ explode('–', $cls->time_slot)[0] ?? $cls->time_slot }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $cls->subject }}</div>
                    <div style="font-size:0.72rem; color:var(--text-muted);">{{ $cls->section }} · {{ $cls->room }}</div>
                </div>
                <span class="pill" style="background:rgba(16,185,129,0.12); color:#34d399; font-size:0.65rem;">Active</span>
            </div>
            @empty
            <div style="text-align:center; padding:2.5rem 1rem; color:var(--text-muted);">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.5rem; opacity:0.4;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <div style="font-size:0.8rem;">No classes scheduled today</div>
                <a href="{{ route('faculty.timetable') }}" style="font-size:0.75rem; color:var(--brand-400);">Set up timetable →</a>
            </div>
            @endforelse
        </div>

        {{-- Evaluation History Chart --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Evaluation History</span>
                <span class="pill pill-indigo">Published</span>
            </div>
            @if($evaluations->isNotEmpty())
            <div class="chart-container" style="height:220px;"><canvas id="evalChart"></canvas></div>
            @else
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">No published evaluations yet.</div>
            @endif
        </div>
    </div>

    {{-- ── Active Goals + Upcoming Workshops ─────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">

        {{-- Active Goals Progress --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Active Goals</span>
                <a href="{{ route('faculty.goals.index') }}" class="btn btn-ghost btn-xs">Manage →</a>
            </div>
            @php
                $activeGoals = \App\Models\Goal::where('faculty_id', (string)auth()->id())->where('status', 'active')->orderByDesc('completion_percentage')->limit(4)->get();
            @endphp
            @forelse($activeGoals as $goal)
            <div style="padding:0.625rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.375rem;">
                    <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:75%;">{{ $goal->title }}</div>
                    <span style="font-size:0.72rem; font-weight:700; color:var(--accent-amber);">{{ number_format($goal->completion_percentage, 0) }}%</span>
                </div>
                <div class="progress-track" style="height:5px;"><div class="progress-fill amber" style="width:{{ min(100, $goal->completion_percentage) }}%;"></div></div>
                <div style="font-size:0.68rem; color:var(--text-muted); margin-top:0.25rem;">Due {{ \Carbon\Carbon::parse($goal->target_date)->diffForHumans() }}</div>
            </div>
            @empty
            <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                No active goals. <a href="{{ route('faculty.goals.index') }}" style="color:var(--brand-400);">Add one →</a>
            </div>
            @endforelse
        </div>

        {{-- Upcoming Workshops --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Upcoming Workshops</span>
                <a href="{{ route('faculty.workshops.index') }}" class="btn btn-ghost btn-xs">Browse All →</a>
            </div>
            @forelse($upcomingWorkshops as $ws)
            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.625rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $ws->title }}</div>
                    <div style="font-size:0.72rem; color:var(--text-muted);">{{ \Carbon\Carbon::parse($ws->schedule_date)->format('d M') }} · +{{ $ws->xp_reward }} XP</div>
                </div>
                <a href="{{ route('faculty.workshops.index') }}" class="btn btn-ghost btn-xs">Register</a>
            </div>
            @empty
            <div style="text-align:center; padding:2rem; color:var(--text-muted);">No upcoming workshops.</div>
            @endforelse
        </div>
    </div>

    {{-- ── Activity Timeline + Wellbeing Trend ────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
        {{-- Activity Timeline --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Recent Achievements</span>
                <a href="{{ route('faculty.achievements.index') }}" class="btn btn-ghost btn-xs">View All</a>
            </div>
            @forelse($achievements as $ach)
            <div class="timeline-item">
                <div class="timeline-dot" style="background:rgba(99,102,241,0.2);color:var(--brand-400); font-size:0.6rem; font-weight:800;">
                    {{ ['publication'=>'PUB','patent'=>'PAT','award'=>'AWD','certification'=>'CERT'][$ach->type] ?? '★' }}
                </div>
                <div class="timeline-body">
                    <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary);">{{ $ach->title }}</div>
                    <div class="timeline-text">{{ $ach->journal_or_body }}</div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-top:2px;">
                        <div class="timeline-time">{{ \Carbon\Carbon::parse($ach->date)->diffForHumans() }}</div>
                        @if($ach->xp_awarded)
                        <span style="font-size:0.65rem; color:var(--accent-amber); font-weight:700;">+{{ $ach->xp_awarded }} XP</span>
                        @endif
                        @if($ach->verified)
                        <span style="font-size:0.62rem; color:#34d399;">✓ Verified</span>
                        @endif
                    </div>
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
            @php
                $latestBurnout = $wellbeingData->last()->burnout_index ?? 0;
                $burnoutColor  = $latestBurnout < 50 ? '#34d399' : ($latestBurnout < 70 ? '#f59e0b' : '#f43f5e');
                $burnoutLabel  = $latestBurnout < 50 ? 'Healthy' : ($latestBurnout < 70 ? 'Moderate' : 'High Risk');
            @endphp
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:0.75rem; padding:0.625rem; background:rgba(255,255,255,0.02); border-radius:10px;">
                <div style="text-align:center;">
                    <div style="font-size:1.5rem; font-weight:800; color:{{ $burnoutColor }};">{{ $latestBurnout }}</div>
                    <div style="font-size:0.65rem; color:{{ $burnoutColor }}; font-weight:600;">{{ $burnoutLabel }}</div>
                </div>
                <div style="font-size:0.75rem; color:var(--text-muted);">Current burnout index. Lower is healthier. Track your trend over time.</div>
            </div>
            <div class="chart-container" style="height:175px;"><canvas id="wellbeingChart"></canvas></div>
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
                    tension:0.4, fill:true, pointRadius:5, pointBackgroundColor:'#818cf8', pointHoverRadius:7,
                },
                {
                    label: 'Teaching',
                    data: @json($evalTeaching),
                    borderColor:'#10b981', backgroundColor:'rgba(16,185,129,0.08)',
                    tension:0.4, fill:true, pointRadius:5, pointBackgroundColor:'#10b981', pointHoverRadius:7,
                },
                {
                    label: 'Innovation',
                    data: @json($evalInnov),
                    borderColor:'#f59e0b', backgroundColor:'rgba(245,158,11,0.08)',
                    tension:0.4, fill:true, pointRadius:5, pointBackgroundColor:'#f59e0b', pointHoverRadius:7,
                },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14, font: { size: 11 } } },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + '%' } }
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
                label: 'Burnout Index',
                data: @json($wbData),
                borderColor: '#f43f5e',
                backgroundColor: 'rgba(244,63,94,0.08)',
                tension: 0.4, fill: true,
                pointRadius: 4, pointBackgroundColor:'#f43f5e', pointHoverRadius:6,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' Burnout: ' + ctx.parsed.y + ' (lower = healthier)' } }
            },
            scales: {
                y: {
                    min: 0, max: 100,
                    reverse: true,
                    ticks: { font: { size: 10 } },
                    grid: { color: 'rgba(255,255,255,0.04)' }
                },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });
    @endif
    </script>
    @endpush
</x-app-layout>
