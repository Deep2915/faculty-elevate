<x-app-layout>
    <x-slot name="header">Attendance Tracker</x-slot>

    @push('styles')
    <style>
    .att-layout { display:grid; grid-template-columns:280px 1fr; gap:1.5rem; align-items:start; }
    .score-ring { position:relative; width:120px; height:120px; margin:0 auto 1rem; }
    .score-ring svg { transform:rotate(-90deg); }
    .score-ring .ring-text { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }

    .status-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 8px; border-radius:99px; font-size:.68rem; font-weight:700; }
    .status-conducted   { background:rgba(16,185,129,.15); color:#6ee7b7; }
    .status-cancelled   { background:rgba(244,63,94,.15);  color:#fda4af; }
    .status-substituted { background:rgba(245,158,11,.15); color:#fcd34d; }

    .timetable-mini { display:flex; flex-direction:column; gap:.375rem; }
    .tt-mini-row { display:flex; align-items:center; gap:.5rem; padding:.4rem .625rem; background:var(--surface-700); border-radius:8px; font-size:.78rem; }
    .tt-mini-day { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--brand-400); width:32px; flex-shrink:0; }
    .tt-mini-sub { flex:1; font-weight:500; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .tt-mini-time { font-size:.65rem; color:var(--text-muted); }

    .override-btn { cursor:pointer; background:transparent; border:1px solid rgba(245,158,11,.3); border-radius:6px; padding:2px 8px; font-size:.7rem; color:#fcd34d; transition:all .18s; }
    .override-btn:hover { background:rgba(245,158,11,.15); }
    </style>
    @endpush

    {{-- Faculty Selector --}}
    <form method="GET" action="{{ route('hod.attendance.index') }}" class="glass-card" style="margin-bottom:1.25rem; padding:1rem 1.25rem;">
        <div style="display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap;">
            <div class="form-group" style="flex:2; min-width:200px;">
                <label>Faculty Member</label>
                <select name="faculty_id" onchange="this.form.submit()">
                    @foreach($faculties as $f)
                    <option value="{{ $f->id }}" @selected($facultyId === (string)$f->id)>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            @if($semesters->isNotEmpty())
            <div class="form-group" style="flex:1; min-width:160px;">
                <label>Semester Filter</label>
                <select name="semester" onchange="this.form.submit()">
                    <option value="">All Semesters</option>
                    @foreach($semesters as $sem)
                    <option value="{{ $sem }}" @selected($semesterFilter === $sem)>{{ $sem }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <button onclick="document.getElementById('addLogModal').style.display='flex'" type="button" class="btn btn-primary" style="white-space:nowrap; align-self:flex-end;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Log
            </button>
        </div>
    </form>

    @if($selectedFaculty)
    <div class="att-layout">

        {{-- Left Panel --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Score --}}
            @php
                $score = $scoreData['score'];
                $pct   = round($score * 100, 1);
                $circ  = 2 * M_PI * 52;
                $offset = $circ * (1 - $score);
                $col   = $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#f43f5e');
            @endphp
            <div class="glass-card" style="text-align:center; padding:1.75rem 1.25rem;">
                <div class="score-ring">
                    <svg viewBox="0 0 120 120" width="120" height="120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="var(--surface-600)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $col }}" stroke-width="10"
                            stroke-linecap="round" stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $circ }}" id="hodRing" data-offset="{{ $offset }}"/>
                    </svg>
                    <div class="ring-text">
                        <span style="font-size:1.5rem; font-weight:800; color:var(--text-primary);">{{ $pct }}%</span>
                        <span style="font-size:.62rem; color:var(--text-muted);">Score</span>
                    </div>
                </div>
                <div style="font-weight:700; color:var(--text-primary); margin-bottom:4px;">{{ $selectedFaculty->name }}</div>
                <div style="font-size:.72rem; color:var(--text-muted);">{{ $selectedFaculty->email }}</div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:.5rem; margin-top:1rem;">
                    @foreach(['conducted'=>'green','cancelled'=>'red','substituted'=>'amber'] as $st => $color)
                    @php $cnt = $logs->where('status',$st)->count(); @endphp
                    <div style="background:var(--surface-700); border-radius:8px; padding:.5rem .375rem; text-align:center;">
                        <div style="font-size:1rem; font-weight:800; color:var(--text-primary);">{{ $cnt }}</div>
                        <div style="font-size:.58rem; color:var(--text-muted);">{{ ucfirst($st) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Timetable Summary --}}
            <div class="glass-card">
                <div class="section-title" style="margin-bottom:.875rem;">Their Timetable</div>
                @if($timetable->isEmpty())
                    <div style="color:var(--text-muted); font-size:.83rem;">No timetable submitted yet.</div>
                @else
                <div class="timetable-mini">
                    @foreach($timetable as $te)
                    <div class="tt-mini-row">
                        <span class="tt-mini-day">{{ substr($te->day_of_week,0,3) }}</span>
                        <span class="tt-mini-sub" title="{{ $te->subject }}">{{ $te->subject }}</span>
                        <span class="tt-mini-time">{{ $te->time_slot }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Monthly Chart --}}
            <div class="glass-card">
                <div class="section-title" style="margin-bottom:.875rem;">Monthly Breakdown</div>
                <div class="chart-container" style="height:180px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Right: Logs Table --}}
        <div class="glass-card" style="padding:0;">
            <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center;">
                <div class="section-title">Class Logs</div>
                <div style="display:flex; gap:.5rem; align-items:center;">
                    <span class="pill pill-indigo">{{ $logs->count() }} records</span>
                    <span style="font-size:.7rem; color:var(--text-muted);">Computation: conducted / total logged</span>
                </div>
            </div>
            <table class="data-table">
                <thead><tr>
                    <th style="padding-left:1.5rem;">Date</th>
                    <th>Subject</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th style="text-align:right; padding-right:1.5rem;">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($logs as $i => $log)
                @php
                    $entry = $timetable->firstWhere('id', $log->timetable_entry_id);
                    $logDate = $log->date instanceof \Carbon\Carbon ? $log->date : \Carbon\Carbon::parse($log->date);
                @endphp
                <tr style="animation:staggerFade .4s ease {{ $i * 0.03 }}s both;">
                    <td style="padding-left:1.5rem; font-weight:600; color:var(--text-primary);">{{ $logDate->format('d M Y') }}</td>
                    <td style="font-size:.83rem;">{{ $entry?->subject ?? '—' }}</td>
                    <td style="color:var(--text-muted); font-size:.78rem;">{{ $entry?->time_slot ?? '—' }}</td>
                    <td>
                        <span class="status-badge status-{{ $log->status }}">
                            {{ $log->status === 'conducted' ? '✓' : ($log->status === 'cancelled' ? '✗' : '⇄') }}
                            {{ ucfirst($log->status) }}
                        </span>
                        @if($log->overridden_by)
                        <span class="pill pill-amber" style="font-size:.6rem; margin-left:3px;" title="Overridden by HOD">HOD</span>
                        @endif
                    </td>
                    <td style="color:var(--text-muted); font-size:.78rem; max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $log->remarks ?? '—' }}</td>
                    <td style="text-align:right; padding-right:1.5rem;">
                        <div style="display:flex; gap:.375rem; justify-content:flex-end;">
                            <button class="override-btn"
                                onclick="openOverride('{{ $log->id }}','{{ $log->status }}','{{ addslashes($log->remarks ?? '') }}')">
                                Override
                            </button>
                            <form method="POST" action="{{ route('hod.attendance.destroy', $log->id) }}" onsubmit="return confirm('Delete this log?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-xs">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:3rem; color:var(--text-muted);">No class logs yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ADD LOG MODAL --}}
    <div id="addLogModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:500px;">
            <div class="modal-title">Add Class Log (HOD)</div>
            <form method="POST" action="{{ route('hod.attendance.add-log') }}">
                @csrf
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Timetable Entry</label>
                        <select name="timetable_entry_id" required>
                            <option value="">Select a class…</option>
                            @foreach($timetable as $te)
                            <option value="{{ $te->id }}">{{ $te->day_of_week }} · {{ $te->subject }} ({{ $te->time_slot }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="conducted">✓ Conducted</option>
                            <option value="cancelled">✗ Cancelled</option>
                            <option value="substituted">⇄ Substituted</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Remarks</label>
                        <textarea name="remarks" rows="2" placeholder="Optional notes…"></textarea>
                    </div>
                </div>
                <div style="display:flex; gap:.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('addLogModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Log</button>
                </div>
            </form>
        </div>
    </div>

    {{-- OVERRIDE MODAL --}}
    <div id="overrideModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:440px;">
            <div class="modal-title">Edit Class Log</div>
            <form id="overrideForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>Status</label>
                    <select name="status" id="overrideStatus" required>
                        <option value="conducted">✓ Conducted</option>
                        <option value="cancelled">✗ Cancelled</option>
                        <option value="substituted">⇄ Substituted</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label>Remarks</label>
                    <textarea name="remarks" id="overrideRemarks" rows="2" placeholder="Reason for override…"></textarea>
                </div>
                <div style="display:flex; gap:.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('overrideModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Override</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    // Animate ring
    const hodRing = document.getElementById('hodRing');
    if (hodRing) {
        const offset = parseFloat(hodRing.dataset.offset);
        requestAnimationFrame(() => {
            hodRing.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1)';
            hodRing.style.strokeDashoffset = offset;
        });
    }

    // Monthly chart
    const monthlyData = @json($monthlyData);
    const chartEl = document.getElementById('monthlyChart');
    if (chartEl) {
        new Chart(chartEl, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.label),
                datasets: [
                    { label: 'Conducted',   data: monthlyData.map(d => d.conducted),   backgroundColor: 'rgba(16,185,129,0.35)', borderColor: '#10b981', borderWidth: 1.5, borderRadius: 4 },
                    { label: 'Cancelled',   data: monthlyData.map(d => d.cancelled),   backgroundColor: 'rgba(244,63,94,0.3)',   borderColor: '#f43f5e', borderWidth: 1.5, borderRadius: 4 },
                    { label: 'Substituted', data: monthlyData.map(d => d.substituted), backgroundColor: 'rgba(245,158,11,0.3)', borderColor: '#f59e0b', borderWidth: 1.5, borderRadius: 4 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#9ca3c8', font: { size: 10 } } } },
                scales: {
                    x: { ticks: { color: '#5b6184', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' }, stacked: true },
                    y: { ticks: { color: '#5b6184', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' }, beginAtZero: true, stacked: true }
                }
            }
        });
    }

    // Override modal
    function openOverride(logId, currentStatus, currentRemarks) {
        document.getElementById('overrideStatus').value = currentStatus;
        document.getElementById('overrideRemarks').value = currentRemarks;
        document.getElementById('overrideForm').action = '/hod/attendance/log/' + logId + '/override';
        document.getElementById('overrideModal').style.display = 'flex';
    }

    </script>
    <style>
    @keyframes staggerFade { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
    </style>
    @endpush
</x-app-layout>
