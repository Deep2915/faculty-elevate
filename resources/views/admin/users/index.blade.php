<x-app-layout>
    <x-slot name="header">User Management</x-slot>

    {{-- Create User Button --}}
    <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem;">
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New User
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="glass-card" style="margin-bottom:1.25rem; padding:1rem 1.25rem;">
        <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:0.75rem; align-items:end;">
            <div class="form-group">
                <label>Search</label>
                <input name="q" value="{{ request('q') }}" placeholder="Name or email…">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="">All Roles</option>
                    @foreach(['admin','hod','faculty'] as $r)
                        <option value="{{ $r }}" @selected(request('role')===$r)>{{ strtoupper($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Sort By</label>
                <select name="sort">
                    <option value="name"  @selected(request('sort','name')==='name')>Name</option>
                    <option value="email" @selected(request('sort')==='email')>Email</option>
                    <option value="role"  @selected(request('sort')==='role')>Role</option>
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

    {{-- Table --}}
    <div class="glass-card" style="padding:0;">
        <table class="data-table">
            <thead><tr>
                <th style="padding-left:1.5rem;">User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th style="text-align:right; padding-right:1.5rem;">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td style="padding-left:1.5rem;">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <div class="avatar" style="width:34px;height:34px;font-size:0.8rem;">{{ strtoupper(substr($u->name,0,1)) }}</div>
                            <span style="font-weight:600; color:var(--text-primary);">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--text-muted);">{{ $u->email }}</td>
                    <td>
                        <span class="pill {{ $u->role==='admin'?'pill-rose':($u->role==='hod'?'pill-amber':'pill-indigo') }}">
                            {{ strtoupper($u->role) }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted);">{{ $u->department_id ?? '—' }}</td>
                    <td style="text-align:right; padding-right:1.5rem;">
                        <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                            <button onclick="openEdit('{{ $u->id }}','{{ addslashes($u->name) }}','{{ $u->email }}','{{ $u->role }}')" class="btn btn-ghost btn-xs">Edit</button>
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" onsubmit="return confirm('Delete {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:2.5rem; color:var(--text-muted);">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem 1.5rem;">{{ $users->links() }}</div>
    </div>

    {{-- ── CREATE MODAL ── --}}
    <div id="createModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-title">➕ Create New User</div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input name="name" placeholder="Dr. Jane Smith" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input name="email" type="email" placeholder="jane@university.edu" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="faculty">Faculty</option>
                            <option value="hod">HOD / Evaluator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input name="password" type="password" placeholder="Min. 8 characters" required>
                    </div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('createModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div id="editModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-title">✏️ Edit User</div>
            <form id="editForm" method="POST" action="">
                @csrf @method('PUT')
                <div class="form-grid" style="grid-template-columns:1fr 1fr; margin-bottom:1rem;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input id="editName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input id="editEmail" name="email" type="email" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select id="editRole" name="role" required>
                            <option value="faculty">Faculty</option>
                            <option value="hod">HOD / Evaluator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>New Password <small style="color:var(--text-muted)">(leave blank to keep)</small></label>
                        <input name="password" type="password" placeholder="Leave blank to keep">
                    </div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function openEdit(id, name, email, role) {
        document.getElementById('editName').value  = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value  = role;
        document.getElementById('editForm').action = '/admin/users/' + id;
        document.getElementById('editModal').style.display = 'flex';
    }
    </script>
    @endpush
</x-app-layout>
