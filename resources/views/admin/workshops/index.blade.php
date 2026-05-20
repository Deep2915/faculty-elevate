<x-app-layout>
    <x-slot name="header">Workshop Management</x-slot>

    <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem;">
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Workshop
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.workshops.index') }}" class="glass-card" style="margin-bottom:1.25rem; padding:1rem 1.25rem;">
        <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:0.75rem; align-items:end;">
            <div class="form-group">
                <label>Search</label>
                <input name="q" value="{{ request('q') }}" placeholder="Title, facilitator, category…">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All</option>
                    @foreach(['upcoming','ongoing','completed'] as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Sort</label>
                <select name="sort">
                    <option value="schedule_date" @selected(request('sort','schedule_date')==='schedule_date')>Date</option>
                    <option value="title"    @selected(request('sort')==='title')>Title</option>
                    <option value="category" @selected(request('sort')==='category')>Category</option>
                </select>
            </div>
            <div class="form-group">
                <label>Direction</label>
                <select name="direction">
                    <option value="asc"  @selected(request('direction','asc')==='asc')>Asc</option>
                    <option value="desc" @selected(request('direction')==='desc')>Desc</option>
                </select>
            </div>
            <button class="btn btn-ghost" type="submit" style="align-self:flex-end;">Filter</button>
        </div>
    </form>

    {{-- Workshop Cards Grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:1.25rem; margin-bottom:1.5rem;">
        @forelse($workshops as $ws)
        <div class="glass-card" style="padding:1.25rem;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
                <div>
                    <div style="font-weight:700; color:var(--text-primary); font-size:0.9375rem; margin-bottom:0.25rem;">{{ $ws->title }}</div>
                    <div style="font-size:0.78rem; color:var(--text-muted);">{{ $ws->facilitator }}</div>
                </div>
                <span class="pill {{ $ws->status==='upcoming'?'pill-indigo':($ws->status==='ongoing'?'pill-amber':'pill-green') }}">
                    {{ ucfirst($ws->status) }}
                </span>
            </div>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.875rem;">
                <span class="pill pill-cyan">{{ $ws->category }}</span>
                <span class="pill pill-gray">{{ optional($ws->schedule_date)->format('d M Y') }}</span>
                <span class="pill pill-amber">{{ $ws->duration_hours }}h</span>
                <span class="pill pill-indigo">Cap: {{ $ws->capacity }}</span>
                <span class="pill pill-green">+{{ $ws->xp_reward }} XP</span>
            </div>
            <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:1rem; line-height:1.5;">{{ Str::limit($ws->description, 90) }}</p>
            <div style="display:flex; gap:0.5rem;">
                <button onclick="openEditWs('{{ $ws->id }}','{{ addslashes($ws->title) }}','{{ addslashes($ws->facilitator) }}','{{ $ws->category }}','{{ optional($ws->schedule_date)->format('Y-m-d') }}','{{ $ws->duration_hours }}','{{ $ws->capacity }}','{{ $ws->xp_reward }}','{{ $ws->status }}','{{ addslashes($ws->description) }}')" class="btn btn-ghost btn-xs">Edit</button>
                <form method="POST" action="{{ route('admin.workshops.destroy', $ws->id) }}" onsubmit="return confirm('Delete this workshop?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-xs">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="glass-card" style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-muted);">
            No workshops found. Create your first one!
        </div>
        @endforelse
    </div>
    <div style="padding:0.5rem 0;">{{ $workshops->links() }}</div>

    {{-- ── CREATE MODAL ── --}}
    <div id="createModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:600px;">
            <div class="modal-title">New Workshop</div>
            <form method="POST" action="{{ route('admin.workshops.store') }}">
                @csrf
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group" style="grid-column:1/-1;"><label>Title</label><input name="title" placeholder="Workshop title" required></div>
                    <div class="form-group"><label>Facilitator</label><input name="facilitator" placeholder="Name of facilitator" required></div>
                    <div class="form-group"><label>Category</label><input name="category" placeholder="e.g. pedagogy, edtech" required></div>
                    <div class="form-group"><label>Schedule Date</label><input type="date" name="schedule_date" required></div>
                    <div class="form-group"><label>Duration (hours)</label><input name="duration_hours" type="number" step="0.5" min="1" placeholder="e.g. 4" required></div>
                    <div class="form-group"><label>Capacity</label><input name="capacity" type="number" min="1" placeholder="Max participants" required></div>
                    <div class="form-group"><label>XP Reward</label><input name="xp_reward" type="number" min="0" placeholder="e.g. 100" required></div>
                    <div class="form-group"><label>Status</label>
                        <select name="status" required>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Description</label><textarea name="description" rows="3" placeholder="What will participants learn?" required></textarea></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('createModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Workshop</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div id="editWsModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box" style="max-width:600px;">
            <div class="modal-title">Edit Workshop</div>
            <form id="editWsForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group" style="grid-column:1/-1;"><label>Title</label><input id="ewTitle" name="title" required></div>
                    <div class="form-group"><label>Facilitator</label><input id="ewFacilitator" name="facilitator" required></div>
                    <div class="form-group"><label>Category</label><input id="ewCategory" name="category" required></div>
                    <div class="form-group"><label>Schedule Date</label><input id="ewDate" type="date" name="schedule_date" required></div>
                    <div class="form-group"><label>Duration (hours)</label><input id="ewDuration" name="duration_hours" type="number" step="0.5" min="1" required></div>
                    <div class="form-group"><label>Capacity</label><input id="ewCapacity" name="capacity" type="number" min="1" required></div>
                    <div class="form-group"><label>XP Reward</label><input id="ewXp" name="xp_reward" type="number" min="0" required></div>
                    <div class="form-group"><label>Status</label>
                        <select id="ewStatus" name="status" required>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Description</label><textarea id="ewDesc" name="description" rows="3" required></textarea></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('editWsModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function openEditWs(id, title, facilitator, category, date, duration, capacity, xp, status, desc) {
        document.getElementById('ewTitle').value       = title;
        document.getElementById('ewFacilitator').value = facilitator;
        document.getElementById('ewCategory').value    = category;
        document.getElementById('ewDate').value        = date;
        document.getElementById('ewDuration').value    = duration;
        document.getElementById('ewCapacity').value    = capacity;
        document.getElementById('ewXp').value          = xp;
        document.getElementById('ewStatus').value      = status;
        document.getElementById('ewDesc').value        = desc;
        document.getElementById('editWsForm').action   = '/admin/workshops/' + id;
        document.getElementById('editWsModal').style.display = 'flex';
    }
    </script>
    @endpush
</x-app-layout>
