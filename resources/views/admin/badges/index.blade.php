<x-app-layout>
    <x-slot name="header">Badge Management</x-slot>

    <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem;">
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Badge
        </button>
    </div>

    {{-- Badge Grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem; margin-bottom:1.5rem;">
        @forelse($badges as $badge)
        <div class="glass-card" style="padding:1.25rem;">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:0.875rem;">
                <div style="width:52px; height:52px; border-radius:14px; background:rgba(99,102,241,0.15); display:flex; align-items:center; justify-content:center; font-size:1.75rem; flex-shrink:0;">
                    @php
                        $icons = ['research'=>'R','teaching'=>'T','innovation'=>'I','attendance'=>'A'];
                    @endphp
                    <span style="font-weight:800; font-size:1.1rem; color:var(--brand-400);">{{ $icons[$badge->category] ?? 'B' }}</span>
                </div>
                <div>
                    <div style="font-weight:700; color:var(--text-primary); font-size:0.9375rem;">{{ $badge->name }}</div>
                    <span class="pill pill-{{ $badge->category==='research'?'cyan':($badge->category==='teaching'?'green':($badge->category==='innovation'?'amber':'indigo')) }}">
                        {{ ucfirst($badge->category) }}
                    </span>
                </div>
            </div>
            <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.875rem; line-height:1.5;">{{ $badge->description }}</p>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span class="pill pill-amber">{{ number_format($badge->xp_threshold) }} XP threshold</span>
                <div style="display:flex; gap:0.5rem;">
                    <button onclick="openEditBadge('{{ $badge->id }}','{{ addslashes($badge->name) }}','{{ addslashes($badge->description) }}','{{ $badge->xp_threshold }}','{{ $badge->category }}')" class="btn btn-ghost btn-xs">Edit</button>
                    <form method="POST" action="{{ route('admin.badges.destroy', $badge->id) }}" onsubmit="return confirm('Delete badge?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-xs">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="glass-card" style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-muted);">
            No badges created yet. Create your first badge to start the gamification engine!
        </div>
        @endforelse
    </div>
    <div>{{ $badges->links() }}</div>

    {{-- CREATE MODAL --}}
    <div id="createModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-title">Create Badge</div>
            <form method="POST" action="{{ route('admin.badges.store') }}">
                @csrf
                <div style="display:flex; flex-direction:column; gap:0.875rem; margin-bottom:1.25rem;">
                    <div class="form-group"><label>Badge Name</label><input name="name" placeholder="e.g. Research Star" required></div>
                    <div class="form-group"><label>Category</label>
                        <select name="category" required>
                            <option value="research">Research</option>
                            <option value="teaching">Teaching</option>
                            <option value="innovation">Innovation</option>
                            <option value="attendance">Attendance</option>
                        </select>
                    </div>
                    <div class="form-group"><label>XP Threshold (awarded when faculty reaches this XP)</label><input name="xp_threshold" type="number" min="0" placeholder="e.g. 500" required></div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="2" placeholder="What this badge represents…" required></textarea></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('createModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Badge</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="editBadgeModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-title">Edit Badge</div>
            <form id="editBadgeForm" method="POST" action="">
                @csrf @method('PUT')
                <div style="display:flex; flex-direction:column; gap:0.875rem; margin-bottom:1.25rem;">
                    <div class="form-group"><label>Badge Name</label><input id="ebName" name="name" required></div>
                    <div class="form-group"><label>Category</label>
                        <select id="ebCategory" name="category" required>
                            <option value="research">Research</option>
                            <option value="teaching">Teaching</option>
                            <option value="innovation">Innovation</option>
                            <option value="attendance">Attendance</option>
                        </select>
                    </div>
                    <div class="form-group"><label>XP Threshold</label><input id="ebXp" name="xp_threshold" type="number" min="0" required></div>
                    <div class="form-group"><label>Description</label><textarea id="ebDesc" name="description" rows="2" required></textarea></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('editBadgeModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function openEditBadge(id, name, desc, xp, cat) {
        document.getElementById('ebName').value     = name;
        document.getElementById('ebDesc').value     = desc;
        document.getElementById('ebXp').value       = xp;
        document.getElementById('ebCategory').value = cat;
        document.getElementById('editBadgeForm').action = '/admin/badges/' + id;
        document.getElementById('editBadgeModal').style.display = 'flex';
    }
    </script>
    @endpush
</x-app-layout>
