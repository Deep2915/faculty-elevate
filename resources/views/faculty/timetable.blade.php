<x-app-layout>
    <x-slot name="header">Timetable & Attendance</x-slot>

    @push('styles')
    <style>
    /* ── Layout ── */
    .tt-kpi-grid  { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.25rem; margin-bottom:1.5rem; }
    .tt-week-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:.625rem; margin-bottom:1.5rem; }

    /* Day column */
    .day-col { background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:14px; overflow:hidden; transition:border-color .2s; }
    .day-col.today { border-color:rgba(99,102,241,.55); box-shadow:0 0 0 1px rgba(99,102,241,.2); }
    .day-header { padding:.625rem .75rem; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; text-align:center; border-bottom:1px solid var(--glass-border); color:var(--text-muted); }
    .day-col.today .day-header { background:rgba(99,102,241,.18); color:var(--brand-400); }
    .class-slot { padding:.5rem .625rem; border-bottom:1px solid rgba(255,255,255,.04); cursor:pointer; transition:background .15s; }
    .class-slot:last-child { border-bottom:none; }
    .class-slot:hover { background:rgba(255,255,255,.04); }
    .class-slot .sub  { font-size:.72rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .class-slot .time { font-size:.62rem; color:var(--text-muted); margin-top:2px; }
    .class-slot .section-tag { font-size:.58rem; font-weight:700; color:var(--brand-400); background:rgba(99,102,241,.12); border-radius:4px; padding:1px 4px; display:inline-block; margin-top:2px; }

    /* Status badges */
    .st-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:99px; font-size:.66rem; font-weight:700; }
    .st-conducted   { background:rgba(16,185,129,.15); color:#6ee7b7; }
    .st-cancelled   { background:rgba(244,63,94,.15);  color:#fda4af; }
    .st-substituted { background:rgba(245,158,11,.15); color:#fcd34d; }

    /* Score ring */
    .score-ring { position:relative; width:90px; height:90px; flex-shrink:0; }
    .score-ring svg { transform:rotate(-90deg); }
    .score-ring .rt { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }

    /* Mark status buttons */
    .mk-btn { flex:1; padding:.5rem; border-radius:8px; font-size:.79rem; font-weight:600; cursor:pointer; border:2px solid transparent; transition:all .18s; background:var(--surface-700); color:var(--text-secondary); }
    .mk-btn.sel-conducted   { border-color:#10b981; background:rgba(16,185,129,.15); color:#6ee7b7; }
    .mk-btn.sel-cancelled   { border-color:#f43f5e; background:rgba(244,63,94,.15);  color:#fda4af; }
    .mk-btn.sel-substituted { border-color:#f59e0b; background:rgba(245,158,11,.15); color:#fcd34d; }

    /* Section code badge */
    .sec-pill { display:inline-flex; align-items:center; gap:4px; background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.3); border-radius:8px; padding:2px 8px; font-size:.72rem; font-weight:700; color:var(--brand-400); font-family:monospace; }

    /* Error list */
    .field-error-msg { background:rgba(244,63,94,.1); border:1px solid rgba(244,63,94,.3); border-radius:8px; padding:.5rem .875rem; font-size:.78rem; color:#fda4af; margin-bottom:.875rem; }
    </style>
    @endpush

    @php
        $days     = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $todayName = \Carbon\Carbon::now()->format('l');
        $score    = $scoreData['score'];
        $pct      = round($score * 100, 1);
        $circ     = 2 * M_PI * 42;
        $offset   = $circ * (1 - $score);
        $ringColor= $pct >= 80 ? '#10b981' : ($pct >= 60 ? '#f59e0b' : '#f43f5e');
    @endphp

    {{-- Flash status --}}
    @if(session('status'))
    <div class="glass-card animate-fade-in" style="padding:.875rem 1.25rem; margin-bottom:1.25rem; border-color:rgba(16,185,129,.3); background:rgba(16,185,129,.08); display:flex; align-items:center; gap:.625rem;">
        <svg width="16" height="16" fill="none" stroke="#6ee7b7" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        <span style="font-size:.85rem; color:#6ee7b7; font-weight:500;">{{ session('status') }}</span>
    </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="field-error-msg animate-fade-in">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- ── Top KPIs ── --}}
    <div class="tt-kpi-grid stagger-children">

        {{-- Score Ring --}}
        <div class="glass-card" style="display:flex; align-items:center; gap:1.25rem; padding:1.25rem 1.5rem;">
            <div class="score-ring">
                <svg viewBox="0 0 90 90" width="90" height="90">
                    <circle cx="45" cy="45" r="42" fill="none" stroke="var(--surface-600)" stroke-width="7"/>
                    <circle cx="45" cy="45" r="42" fill="none" stroke="{{ $ringColor }}" stroke-width="7"
                        stroke-linecap="round" stroke-dasharray="{{ $circ }}"
                        stroke-dashoffset="{{ $circ }}" id="scoreRing" data-offset="{{ $offset }}"/>
                </svg>
                <div class="rt">
                    <span style="font-size:1.1rem; font-weight:800; color:var(--text-primary);">{{ $pct }}%</span>
                </div>
            </div>
            <div>
                <div style="font-size:1rem; font-weight:700; color:var(--text-primary);">Attendance</div>
                <div style="font-size:.75rem; color:var(--text-muted); margin-top:2px;">{{ $scoreData['conducted'] }} / {{ $scoreData['total'] }} classes</div>
                <div style="margin-top:.5rem;">
                    @if($pct >= 80) <span class="st-badge st-conducted">Excellent</span>
                    @elseif($pct >= 60) <span class="st-badge st-substituted">Average</span>
                    @else <span class="st-badge st-cancelled">Low</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Today --}}
        <div class="glass-card" style="padding:1.25rem 1.5rem;">
            <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.75rem;">Today — {{ $todayName }}</div>
            @forelse($todayEntries as $te)
            <div style="display:flex; align-items:center; gap:.625rem; padding:.375rem 0; border-bottom:1px solid rgba(255,255,255,.04);">
                <div style="flex:1; overflow:hidden;">
                    <div style="font-size:.82rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $te->subject }}</div>
                    <div style="font-size:.68rem; color:var(--text-muted);">{{ $te->time_slot }}{{ $te->room ? ' · '.$te->room : '' }}</div>
                    @if($te->section)<span class="sec-pill">{{ $te->section }}</span>@endif
                </div>
                <button onclick="openMark('{{ $te->id }}','{{ addslashes($te->subject) }}','{{ $te->time_slot }}','{{ $te->section ?? '' }}')"
                    class="btn btn-primary btn-xs" style="white-space:nowrap;">Mark</button>
            </div>
            @empty
            <div style="color:var(--text-muted); font-size:.82rem; padding:.5rem 0;">No classes today.</div>
            @endforelse
        </div>

        {{-- Quick Stats --}}
        <div class="glass-card" style="padding:1.25rem 1.5rem;">
            <div style="font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.75rem;">Last 14 Days</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem;">
                @foreach(['conducted'=>['green','#6ee7b7'],'cancelled'=>['red','#fda4af'],'substituted'=>['amber','#fcd34d']] as $s => [$cls,$col])
                @php $cnt = $recentLogs->where('status',$s)->count(); @endphp
                <div style="background:var(--surface-700); border-radius:10px; padding:.5rem .625rem; text-align:center;">
                    <div style="font-size:1.2rem; font-weight:800; color:{{ $col }};">{{ $cnt }}</div>
                    <div style="font-size:.63rem; color:var(--text-muted);">{{ ucfirst($s) }}</div>
                </div>
                @endforeach
                <div style="background:var(--surface-700); border-radius:10px; padding:.5rem .625rem; text-align:center;">
                    <div style="font-size:1.2rem; font-weight:800; color:var(--text-primary);">{{ $entries->count() }}</div>
                    <div style="font-size:.63rem; color:var(--text-muted);">Scheduled</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Weekly Timetable Grid ── --}}
    <div class="glass-card" style="margin-bottom:1.5rem; padding:1.25rem;">
        <div class="section-header">
            <div class="section-title">Weekly Schedule</div>
            <button onclick="document.getElementById('addClassModal').style.display='flex'" class="btn btn-primary btn-sm">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Class
            </button>
        </div>
        <div class="tt-week-grid" style="margin-top:1rem;">
            @foreach($days as $day)
            <div class="day-col {{ $day === $todayName ? 'today' : '' }}">
                <div class="day-header">{{ substr($day,0,3) }}@if($day===$todayName) ●@endif</div>
                @forelse($weekGrid[$day] ?? [] as $te)
                <div class="class-slot" onclick="openMark('{{ $te->id }}','{{ addslashes($te->subject) }}','{{ $te->time_slot }}','{{ $te->section ?? '' }}')">
                    <div class="sub" title="{{ $te->subject }}">{{ $te->subject }}</div>
                    <div class="time">{{ $te->time_slot }}</div>
                    @if($te->section)<div class="section-tag">{{ $te->section }}</div>@endif
                </div>
                @empty
                <div style="padding:.875rem .5rem; text-align:center; font-size:.65rem; color:rgba(91,97,132,.4);">—</div>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Recent Class Logs ── --}}
    <div class="glass-card" style="padding:0; margin-bottom:1.25rem;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center;">
            <div class="section-title">Recent Class Log <span style="font-size:.72rem; color:var(--text-muted); font-weight:400;">(last 14 days)</span></div>
            <span class="pill pill-indigo">{{ $recentLogs->count() }} entries</span>
        </div>
        <table class="data-table">
            <thead><tr>
                <th style="padding-left:1.5rem;">Date</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Time</th>
                <th>Status</th>
                <th>Remarks</th>
                <th style="text-align:right; padding-right:1.5rem;">Edit</th>
            </tr></thead>
            <tbody>
            @forelse($recentLogs as $log)
            @php
                $entry   = $entries->firstWhere('id', $log->timetable_entry_id);
                $logDate = $log->date instanceof \Carbon\Carbon ? $log->date : \Carbon\Carbon::parse($log->date);
            @endphp
            <tr>
                <td style="padding-left:1.5rem; font-weight:600; color:var(--text-primary);">{{ $logDate->format('d M') }}</td>
                <td style="font-size:.82rem; color:var(--text-primary);">{{ $entry?->subject ?? '—' }}</td>
                <td>@if($entry?->section)<span class="sec-pill">{{ $entry->section }}</span>@else<span style="color:var(--text-muted);">—</span>@endif</td>
                <td style="color:var(--text-muted); font-size:.78rem;">{{ $entry?->time_slot ?? '—' }}</td>
                <td>
                    <span class="st-badge st-{{ $log->status }}">
                        {{ $log->status === 'conducted' ? '&#10003;' : ($log->status === 'cancelled' ? '&#10007;' : '&#8644;') }}
                        {{ ucfirst($log->status) }}
                    </span>
                    @if($log->overridden_by)<span class="pill pill-amber" style="font-size:.58rem; margin-left:3px;">HOD</span>@endif
                </td>
                <td style="color:var(--text-muted); font-size:.75rem; max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $log->remarks ?? '—' }}</td>
                <td style="text-align:right; padding-right:1.5rem;">
                    <button onclick="openMark('{{ $log->timetable_entry_id }}','{{ addslashes($entry?->subject ?? '') }}','{{ $entry?->time_slot ?? '' }}','{{ $entry?->section ?? '' }}','{{ $logDate->format('Y-m-d') }}','{{ $log->status }}')"
                        class="btn btn-ghost btn-xs">Edit</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; padding:2.5rem; color:var(--text-muted);">No logs yet. Click <strong>Mark</strong> on any class to start logging.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Scheduled Classes List ── --}}
    @if($entries->isNotEmpty())
    <div class="glass-card" style="padding:0;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--glass-border);">
            <div class="section-title">My Scheduled Classes</div>
        </div>
        <table class="data-table">
            <thead><tr>
                <th style="padding-left:1.5rem;">Subject</th>
                <th>Section</th>
                <th>Day</th>
                <th>Time Slot</th>
                <th>Room</th>
                <th>Semester</th>
                <th style="text-align:right; padding-right:1.5rem;">Remove</th>
            </tr></thead>
            <tbody>
            @foreach($entries as $te)
            <tr>
                <td style="padding-left:1.5rem; font-weight:600; color:var(--text-primary);">{{ $te->subject }}</td>
                <td>@if($te->section)<span class="sec-pill">{{ $te->section }}</span>@else<span style="color:var(--text-muted);">—</span>@endif</td>
                <td style="color:var(--text-muted);">{{ $te->day_of_week }}</td>
                <td style="color:var(--text-muted);">{{ $te->time_slot }}</td>
                <td style="color:var(--text-muted);">{{ $te->room ?? '—' }}</td>
                <td><span class="pill pill-gray">{{ $te->semester }}</span></td>
                <td style="text-align:right; padding-right:1.5rem;">
                    <form method="POST" action="{{ route('faculty.timetable.destroy', $te->id) }}" onsubmit="return confirm('Remove {{ addslashes($te->subject) }} from timetable?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-xs">Remove</button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ══ ADD CLASS MODAL ══ --}}
    <div id="addClassModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:580px;">
            <div class="modal-title">Add Class to Timetable</div>

            <div style="background:rgba(99,102,241,.08); border:1px solid rgba(99,102,241,.2); border-radius:10px; padding:.625rem 1rem; margin-bottom:1rem; font-size:.78rem; color:var(--text-secondary);">
                <strong>Section format:</strong> Program + Batch year + Section code &nbsp;&mdash;&nbsp; e.g. <code style="color:var(--brand-400);">K23RK</code> = BCA, batch 2023-27, section RK
            </div>

            <form method="POST" action="{{ route('faculty.timetable.store') }}" id="addClassForm">
                @csrf
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:.75rem;">

                    {{-- Subject --}}
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Subject <span style="color:#f43f5e;">*</span></label>
                        <select name="subject" required>
                            <option value="">— Select Subject —</option>
                            @foreach($subjects as $sub)
                            <option value="{{ $sub }}" {{ old('subject') === $sub ? 'selected' : '' }}>{{ $sub }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Day --}}
                    <div class="form-group">
                        <label>Day of Week <span style="color:#f43f5e;">*</span></label>
                        <select name="day_of_week" required>
                            <option value="">— Select Day —</option>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d)
                            <option value="{{ $d }}" {{ old('day_of_week', $todayName) === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Time Slot --}}
                    <div class="form-group">
                        <label>Time Slot <span style="color:#f43f5e;">*</span></label>
                        <select name="time_slot" required>
                            <option value="">— Select Time —</option>
                            @foreach($timeSlots as $ts)
                            <option value="{{ $ts }}" {{ old('time_slot') === $ts ? 'selected' : '' }}>{{ $ts }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Section --}}
                    <div class="form-group">
                        <label>Section <span style="color:#f43f5e;">*</span>
                            <span style="font-size:.68rem; color:var(--text-muted); font-weight:400;">e.g. K23RK</span>
                        </label>
                        <select name="section" required id="sectionSelect">
                            <option value="">— Select Section —</option>
                            @foreach($sections as $sec)
                            <option value="{{ $sec }}" {{ old('section') === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Semester --}}
                    <div class="form-group">
                        <label>Semester <span style="color:#f43f5e;">*</span></label>
                        <select name="semester" required>
                            <option value="">— Select Semester —</option>
                            @foreach($semesters as $sem)
                            <option value="{{ $sem }}" {{ old('semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Room --}}
                    <div class="form-group">
                        <label>Room / Hall <span style="color:var(--text-muted); font-weight:400; font-size:.75rem;">(optional)</span></label>
                        <select name="room">
                            <option value="">— No Room —</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room }}" {{ old('room') === $room ? 'selected' : '' }}>{{ $room }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Clash warning placeholder --}}
                <div id="clashWarning" style="display:none; background:rgba(244,63,94,.1); border:1px solid rgba(244,63,94,.3); border-radius:8px; padding:.5rem .875rem; font-size:.78rem; color:#fda4af; margin-bottom:.875rem;">
                    Checking for clashes…
                </div>

                <div style="display:flex; gap:.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('addClassModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addClassBtn">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add to Timetable
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ MARK CLASS MODAL ══ --}}
    <div id="markModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:460px;">
            <div class="modal-title" id="markTitle">Mark Class</div>
            <div id="markSection" style="margin-bottom:.875rem; display:none;">
                <span class="sec-pill" id="markSectionBadge"></span>
            </div>
            <form method="POST" action="{{ route('faculty.timetable.mark') }}" id="markForm">
                @csrf
                <input type="hidden" name="timetable_entry_id" id="markEntryId">
                <div class="form-group" style="margin-bottom:.875rem;">
                    <label>Date</label>
                    <input type="date" name="date" id="markDate" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group" style="margin-bottom:.875rem;">
                    <label>Class Status <span style="color:#f43f5e;">*</span></label>
                    <div style="display:flex; gap:.5rem; margin-top:.375rem;">
                        <button type="button" class="mk-btn" id="mkConducted"   onclick="selStatus('conducted')">Conducted</button>
                        <button type="button" class="mk-btn" id="mkCancelled"   onclick="selStatus('cancelled')">Cancelled</button>
                        <button type="button" class="mk-btn" id="mkSubstituted" onclick="selStatus('substituted')">Substituted</button>
                    </div>
                    <input type="hidden" name="status" id="markStatus" required>
                </div>
                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label>Remarks <span style="font-size:.72rem; color:var(--text-muted); font-weight:400;">(optional)</span></label>
                    <textarea name="remarks" id="markRemarks" rows="2" placeholder="e.g. Guest lecture, lab session moved…"></textarea>
                </div>
                <div style="display:flex; gap:.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('markModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" id="mkSubmit" class="btn btn-primary" disabled>Save Log</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    // Score ring animation
    const ring = document.getElementById('scoreRing');
    if (ring) {
        const off = parseFloat(ring.dataset.offset);
        requestAnimationFrame(() => { ring.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1)'; ring.style.strokeDashoffset = off; });
    }

    // Open ADD modal when redirected back with errors
    @if($errors->any())
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('addClassModal').style.display='flex'; });
    @endif

    // ── Mark modal ──
    function openMark(entryId, subject, timeSlot, section, date, currentStatus) {
        document.getElementById('markEntryId').value = entryId;
        document.getElementById('markTitle').textContent = subject + ' (' + timeSlot + ')';
        document.getElementById('markDate').value = date || new Date().toISOString().slice(0, 10);
        document.getElementById('markRemarks').value = '';
        document.getElementById('markStatus').value = '';
        document.getElementById('mkSubmit').disabled = true;
        ['Conducted','Cancelled','Substituted'].forEach(s => { document.getElementById('mk'+s).className='mk-btn'; });

        // Section badge
        const secBadge = document.getElementById('markSectionBadge');
        const secDiv   = document.getElementById('markSection');
        if (section) { secBadge.textContent = section; secDiv.style.display='block'; }
        else { secDiv.style.display='none'; }

        if (currentStatus) selStatus(currentStatus);
        document.getElementById('markModal').style.display = 'flex';
    }

    function selStatus(status) {
        document.getElementById('markStatus').value = status;
        document.getElementById('mkSubmit').disabled = false;
        ['Conducted','Cancelled','Substituted'].forEach(s => { document.getElementById('mk'+s).className='mk-btn'; });
        const cap = status.charAt(0).toUpperCase() + status.slice(1);
        document.getElementById('mk'+cap).className = 'mk-btn sel-' + status;
    }
    </script>
    @endpush
</x-app-layout>
