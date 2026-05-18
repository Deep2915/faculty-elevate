<x-app-layout>
    <x-slot name="header">Admin Dashboard</x-slot>

    {{-- KPI Row --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-bottom:1.75rem;">
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(99,102,241,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalFaculty }}</div>
                    <div class="kpi-label">Faculty Members</div>
                </div>
            </div>
        </div>
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(16,185,129,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalWorkshops }}</div>
                    <div class="kpi-label">Workshops</div>
                </div>
            </div>
        </div>
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(245,158,11,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalEvals }}</div>
                    <div class="kpi-label">Evaluations</div>
                </div>
            </div>
        </div>
        <div class="kpi-card">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div class="kpi-icon" style="background:rgba(6,182,212,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#06b6d4" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalHOD }}</div>
                    <div class="kpi-label">HOD / Evaluators</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.75rem;">
        {{-- PI Distribution Chart --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Performance Index Distribution</span>
                <span class="pill pill-indigo">All Faculty</span>
            </div>
            <div class="chart-container" style="height:240px;">
                <canvas id="piChart"></canvas>
            </div>
        </div>

        {{-- Workshop Status Donut --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Workshop Status</span>
                <span class="pill pill-green">Live</span>
            </div>
            <div class="chart-container" style="height:240px; display:flex; align-items:center; justify-content:center;">
                <canvas id="wsChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Performers + Recent Users --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
        {{-- Top Performers --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">🏆 Top Performers</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-xs">View All</a>
            </div>
            <table class="data-table">
                <thead><tr>
                    <th>Rank</th><th>Faculty</th><th>PI Score</th><th>XP</th>
                </tr></thead>
                <tbody>
                    @foreach($topPerformers as $i => $profile)
                    <tr>
                        <td>
                            @if($i===0) <span class="rank-1">🥇 1</span>
                            @elseif($i===1) <span class="rank-2">🥈 2</span>
                            @elseif($i===2) <span class="rank-3">🥉 3</span>
                            @else <span style="color:var(--text-muted);">{{ $i+1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div class="avatar" style="width:28px;height:28px;font-size:0.7rem;">{{ strtoupper(substr($profile->user->name,0,1)) }}</div>
                                <span style="font-weight:500; color:var(--text-primary);">{{ $profile->user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div class="progress-track" style="width:80px;">
                                    <div class="progress-fill" style="width:{{ min(100, ($profile->performance_index??0)*100) }}%"></div>
                                </div>
                                <span style="font-size:0.8rem; color:var(--brand-400);">{{ number_format(($profile->performance_index??0)*100,1) }}%</span>
                            </div>
                        </td>
                        <td><span class="pill pill-amber">{{ number_format($profile->xp??0) }} XP</span></td>
                    </tr>
                    @endforeach
                    @if($topPerformers->isEmpty())
                    <tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding:2rem;">No profiles yet. Add faculty & evaluations.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Recent Users --}}
        <div class="glass-card">
            <div class="section-header">
                <span class="section-title">Recent Users</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-xs">Manage</a>
            </div>
            <table class="data-table">
                <thead><tr><th>User</th><th>Role</th><th>Email</th></tr></thead>
                <tbody>
                    @foreach($recentUsers as $u)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div class="avatar" style="width:28px;height:28px;font-size:0.7rem;">{{ strtoupper(substr($u->name,0,1)) }}</div>
                                <span style="font-weight:500; color:var(--text-primary);">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="pill {{ $u->role==='admin'?'pill-rose':($u->role==='hod'?'pill-amber':'pill-indigo') }}">
                                {{ strtoupper($u->role) }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem; color:var(--text-muted);">{{ $u->email }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    Chart.defaults.color = '#9ca3c8';
    Chart.defaults.borderColor = 'rgba(129,140,248,0.1)';

    // PI Distribution Bar
    new Chart(document.getElementById('piChart'), {
        type: 'bar',
        data: {
            labels: ['0–20', '20–40', '40–60', '60–80', '80–100'],
            datasets: [{
                label: 'Faculty Count',
                data: @json($piData),
                backgroundColor: [
                    'rgba(244,63,94,0.7)', 'rgba(245,158,11,0.7)',
                    'rgba(99,102,241,0.7)', 'rgba(16,185,129,0.7)', 'rgba(6,182,212,0.7)'
                ],
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' } },
                y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { stepSize: 1 } }
            }
        }
    });

    // Workshop Donut
    new Chart(document.getElementById('wsChart'), {
        type: 'doughnut',
        data: {
            labels: ['Upcoming', 'Ongoing', 'Completed'],
            datasets: [{
                data: [{{ $wsUpcoming }}, {{ $wsOngoing }}, {{ $wsCompleted }}],
                backgroundColor: ['rgba(99,102,241,0.8)', 'rgba(245,158,11,0.8)', 'rgba(16,185,129,0.8)'],
                borderColor: 'rgba(28,31,53,1)',
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 8 } } }
        }
    });
    </script>
    @endpush
</x-app-layout>
