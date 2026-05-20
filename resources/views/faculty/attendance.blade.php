<x-app-layout>
    <x-slot name="header">My Attendance</x-slot>

    @push('styles')
    <style>
    .att-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.25rem; margin-bottom:1.5rem; }
    .att-kpi { background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:14px; padding:1.25rem 1.5rem; text-align:center; transition:transform .2s, box-shadow .2s; }
    .att-kpi:hover { transform:translateY(-3px); box-shadow:0 12px 40px rgba(0,0,0,.35); }
    .att-kpi-value { font-size:2rem; font-weight:800; color:var(--text-primary); }
    .att-kpi-label { font-size:0.78rem; color:var(--text-muted); margin-top:4px; }
    .att-row-bar { display:flex; align-items:center; gap:0.75rem; }
    </style>
    @endpush

    @php
        $pct = round($score * 100, 1);
        $ringColor = $pct >= 80 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#f43f5e');
        $circumference = 2 * M_PI * 52;
        $offset = $circumference * (1 - $score);
    @endphp

    {{-- KPIs --}}
    <div class="att-grid">
        <div class="att-kpi">
            <div class="att-kpi-value" style="color:{{ $pct >= 80 ? 'var(--accent-green)' : ($pct >= 50 ? 'var(--accent-amber)' : 'var(--accent-rose)') }};">{{ $pct }}%</div>
            <div class="att-kpi-label">Overall Attendance Score</div>
        </div>
        <div class="att-kpi">
            <div class="att-kpi-value">{{ number_format($totalScheduled, 1) }}h</div>
            <div class="att-kpi-label">Total Scheduled Hours</div>
        </div>
        <div class="att-kpi">
            <div class="att-kpi-value" style="color:var(--accent-green);">{{ number_format($totalActual, 1) }}h</div>
            <div class="att-kpi-label">Total Hours Attended</div>
        </div>
        <div class="att-kpi">
            <div class="att-kpi-value">{{ $logs->count() }}</div>
            <div class="att-kpi-label">Session Records</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 2fr; gap:1.5rem; align-items:start;">

        {{-- Score Ring & Chart --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            <div class="glass-card" style="text-align:center; padding:2rem 1.5rem;">
                <div style="position:relative; width:140px; height:140px; margin:0 auto;">
                    <svg viewBox="0 0 120 120" width="140" height="140" style="transform:rotate(-90deg);">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="var(--surface-600)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none"
                            stroke="{{ $ringColor }}" stroke-width="10" stroke-linecap="round"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $circumference }}"
                            id="myRing" data-offset="{{ $offset }}"/>
                    </svg>
                    <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span style="font-size:1.75rem; font-weight:800; color:var(--text-primary);">{{ $pct }}%</span>
                        <span style="font-size:0.65rem; color:var(--text-muted);">Score</span>
                    </div>
                </div>
                <div style="margin-top:1rem; font-size:0.85rem; color:var(--text-secondary);">
                    @if($pct >= 80)
                        <strong>Excellent</strong> attendance record
                    @elseif($pct >= 60)
                        <strong>Good</strong> — keep it up
                    @else
                        <strong>Needs improvement</strong>
                    @endif
                </div>
            </div>
            <div class="glass-card">
                <div class="section-title" style="margin-bottom:1rem;">6-Month Trend</div>
                <div class="chart-container" style="height:160px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Logs Table --}}
        <div class="glass-card" style="padding:0;">
            <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--glass-border);">
                <div class="section-title">Session History</div>
            </div>
            <table class="data-table">
                <thead><tr>
                    <th style="padding-left:1.5rem;">Date</th>
                    <th>Scheduled</th>
                    <th>Actual</th>
                    <th>Attendance %</th>
                    <th>Note</th>
                </tr></thead>
                <tbody>
                @forelse($logs as $log)
                @php
                    $sch = (float)$log->scheduled_hours;
                    $act = (float)$log->actual_hours;
                    $r   = $sch > 0 ? min(1.0, $act/$sch) : 0;
                    $logDate = $log->date instanceof \Carbon\Carbon ? $log->date : \Carbon\Carbon::parse($log->date);
                @endphp
                <tr>
                    <td style="padding-left:1.5rem; font-weight:600; color:var(--text-primary);">{{ $logDate->format('d M Y') }}</td>
                    <td>{{ $sch }}h</td>
                    <td>{{ $act }}h</td>
                    <td>
                        <div class="att-row-bar">
                            <div class="progress-track" style="width:70px;"><div class="progress-fill {{ $r >= 1 ? 'green' : ($r >= 0.75 ? '' : 'rose') }}" style="width:{{ round($r*100) }}%"></div></div>
                            <span style="font-size:0.78rem; font-weight:600; color:{{ $r >= 1 ? 'var(--accent-green)' : ($r >= 0.75 ? 'var(--accent-amber)' : 'var(--accent-rose)') }};">{{ round($r*100) }}%</span>
                        </div>
                    </td>
                    <td style="color:var(--text-muted); font-size:0.8rem;">{{ $log->note ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:3rem; color:var(--text-muted);">No attendance records available yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    const myRing = document.getElementById('myRing');
    if (myRing) {
        const offset = parseFloat(myRing.dataset.offset);
        requestAnimationFrame(() => {
            myRing.style.transition = 'stroke-dashoffset 1.3s cubic-bezier(0.4,0,0.2,1)';
            myRing.style.strokeDashoffset = offset;
        });
    }
    const monthly = @json($monthlyData);
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: monthly.map(d => d.label),
            datasets: [
                { label: 'Scheduled', data: monthly.map(d => d.scheduled), borderColor:'#6366f1', backgroundColor:'rgba(99,102,241,0.1)', tension:.4, fill:true },
                { label: 'Actual', data: monthly.map(d => d.actual), borderColor:'#10b981', backgroundColor:'rgba(16,185,129,0.1)', tension:.4, fill:true },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color:'#9ca3c8', font:{ size:10 } } } },
            scales: {
                x: { ticks: { color:'#5b6184', font:{size:10} }, grid:{ color:'rgba(255,255,255,0.04)' } },
                y: { ticks: { color:'#5b6184', font:{size:10} }, grid:{ color:'rgba(255,255,255,0.04)' }, beginAtZero: true }
            }
        }
    });
    </script>
    @endpush
</x-app-layout>
