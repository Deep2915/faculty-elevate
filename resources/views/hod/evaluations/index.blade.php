<x-app-layout>
    <x-slot name="header">Evaluations</x-slot>

    <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem;">
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Evaluation
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('hod.evaluations.index') }}" class="glass-card" style="margin-bottom:1.25rem; padding:1rem 1.25rem;">
        <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:0.75rem; align-items:end;">
            <div class="form-group"><label>Search</label><input name="q" value="{{ request('q') }}" placeholder="Faculty ID or period…"></div>
            <div class="form-group"><label>Status</label>
                <select name="status">
                    <option value="">All</option>
                    <option value="draft" @selected(request('status')==='draft')>Draft</option>
                    <option value="published" @selected(request('status')==='published')>Published</option>
                </select>
            </div>
            <div class="form-group"><label>Sort By</label>
                <select name="sort">
                    <option value="created_at" @selected(request('sort','created_at')==='created_at')>Created</option>
                    <option value="weighted_score" @selected(request('sort')==='weighted_score')>Score</option>
                    <option value="period" @selected(request('sort')==='period')>Period</option>
                </select>
            </div>
            <div class="form-group"><label>Direction</label>
                <select name="direction">
                    <option value="desc" @selected(request('direction','desc')==='desc')>Desc</option>
                    <option value="asc" @selected(request('direction')==='asc')>Asc</option>
                </select>
            </div>
            <button class="btn btn-ghost" type="submit" style="align-self:flex-end;">Filter</button>
        </div>
    </form>

    {{-- Evaluations Table --}}
    <div class="glass-card" style="padding:0;">
        <table class="data-table">
            <thead><tr>
                <th style="padding-left:1.5rem;">Faculty</th>
                <th>Period</th>
                <th>Research</th><th>Teaching</th><th>Innovation</th>
                <th>Weighted Score</th>
                <th>Status</th>
                <th style="text-align:right; padding-right:1.5rem;">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($evaluations as $eval)
                @php
                    $faculty = $faculties->firstWhere('id', $eval->faculty_id);
                    $score   = (float)($eval->weighted_score ?? 0);
                    $pct     = round($score * 100, 1);
                    $pillCls = $pct >= 80 ? 'pill-green' : ($pct >= 50 ? 'pill-amber' : 'pill-rose');
                @endphp
                <tr>
                    <td style="padding-left:1.5rem;">
                        <div style="display:flex; align-items:center; gap:0.625rem;">
                            <div class="avatar" style="width:30px;height:30px;font-size:0.72rem;">{{ strtoupper(substr($faculty->name ?? '?', 0, 1)) }}</div>
                            <span style="font-weight:600; color:var(--text-primary);">{{ $faculty->name ?? $eval->faculty_id }}</span>
                        </div>
                    </td>
                    <td><span class="pill pill-gray">{{ $eval->period }}</span></td>
                    <td style="color:var(--text-secondary);">{{ number_format(data_get($eval,'scores.research',0)*100,0) }}%</td>
                    <td style="color:var(--text-secondary);">{{ number_format(data_get($eval,'scores.teaching',0)*100,0) }}%</td>
                    <td style="color:var(--text-secondary);">{{ number_format(data_get($eval,'scores.innovation',0)*100,0) }}%</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <div class="progress-track" style="width:80px;"><div class="progress-fill" style="width:{{ $pct }}%"></div></div>
                            <span class="pill {{ $pillCls }}">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td><span class="pill {{ $eval->status==='published'?'pill-green':'pill-amber' }}">{{ ucfirst($eval->status) }}</span></td>
                    <td style="text-align:right; padding-right:1.5rem;">
                        <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                            <button onclick="openEdit('{{ $eval->id }}','{{ $eval->faculty_id }}','{{ $eval->period }}','{{ data_get($eval,'scores.research',0) }}','{{ data_get($eval,'scores.teaching',0) }}','{{ data_get($eval,'scores.innovation',0) }}','{{ data_get($eval,'scores.student_clarity',0) }}','{{ data_get($eval,'scores.attendance',0) }}','{{ $eval->status }}','{{ addslashes($eval->remarks) }}')" class="btn btn-ghost btn-xs">Edit</button>
                            <form method="POST" action="{{ route('hod.evaluations.destroy', $eval->id) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-xs">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; padding:2.5rem; color:var(--text-muted);">No evaluations yet. Create the first one.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem 1.5rem;">{{ $evaluations->links() }}</div>
    </div>

    {{-- CREATE MODAL --}}
    <div id="createModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:580px;">
            <div class="modal-title">📝 New Evaluation</div>
            <form method="POST" action="{{ route('hod.evaluations.store') }}">
                @csrf
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group" style="grid-column:1/-1;"><label>Faculty</label>
                        <select name="faculty_id" required>
                            <option value="">Select Faculty…</option>
                            @foreach($faculties as $f)
                            <option value="{{ $f->id }}">{{ $f->name }} — {{ $f->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Period</label><input name="period" placeholder="e.g. Q1-2026 / Annual-2025" required></div>
                    <div class="form-group"><label>Status</label>
                        <select name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Research Score (0–1)</label><input name="scores[research]" type="number" min="0" max="1" step="0.01" placeholder="0.00 – 1.00" required></div>
                    <div class="form-group"><label>Teaching Score (0–1)</label><input name="scores[teaching]" type="number" min="0" max="1" step="0.01" placeholder="0.00 – 1.00" required></div>
                    <div class="form-group"><label>Innovation Score (0–1)</label><input name="scores[innovation]" type="number" min="0" max="1" step="0.01" placeholder="0.00 – 1.00" required></div>
                    <div class="form-group"><label>Student Clarity (0–1)</label><input name="scores[student_clarity]" type="number" min="0" max="1" step="0.01" placeholder="0.00 – 1.00"></div>
                    <div class="form-group"><label>Attendance (0–1)</label><input name="scores[attendance]" type="number" min="0" max="1" step="0.01" placeholder="0.00 – 1.00"></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>HOD Remarks</label><textarea name="remarks" rows="3" placeholder="Observations and feedback…"></textarea></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('createModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Evaluation</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="editModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:580px;">
            <div class="modal-title">✏️ Edit Evaluation</div>
            <form id="editForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group" style="grid-column:1/-1;"><label>Faculty</label>
                        <select id="eFaculty" name="faculty_id" required>
                            @foreach($faculties as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Period</label><input id="ePeriod" name="period" required></div>
                    <div class="form-group"><label>Status</label>
                        <select id="eStatus" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Research (0–1)</label><input id="eRes" name="scores[research]" type="number" min="0" max="1" step="0.01" required></div>
                    <div class="form-group"><label>Teaching (0–1)</label><input id="eTeach" name="scores[teaching]" type="number" min="0" max="1" step="0.01" required></div>
                    <div class="form-group"><label>Innovation (0–1)</label><input id="eInnov" name="scores[innovation]" type="number" min="0" max="1" step="0.01" required></div>
                    <div class="form-group"><label>Student Clarity (0–1)</label><input id="eSC" name="scores[student_clarity]" type="number" min="0" max="1" step="0.01"></div>
                    <div class="form-group"><label>Attendance (0–1)</label><input id="eAtt" name="scores[attendance]" type="number" min="0" max="1" step="0.01"></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>HOD Remarks</label><textarea id="eRemarks" name="remarks" rows="3"></textarea></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function openEdit(id, fid, period, res, teach, innov, sc, att, status, remarks) {
        document.getElementById('eFaculty').value = fid;
        document.getElementById('ePeriod').value  = period;
        document.getElementById('eRes').value     = res;
        document.getElementById('eTeach').value   = teach;
        document.getElementById('eInnov').value   = innov;
        document.getElementById('eSC').value      = sc;
        document.getElementById('eAtt').value     = att;
        document.getElementById('eStatus').value  = status;
        document.getElementById('eRemarks').value = remarks;
        document.getElementById('editForm').action = '/hod/evaluations/' + id;
        document.getElementById('editModal').style.display = 'flex';
    }
    </script>
    @endpush
</x-app-layout>
