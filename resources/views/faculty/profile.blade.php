<x-app-layout>
    <x-slot name="header">My Profile</x-slot>

    <div style="display:grid; grid-template-columns:1fr 2fr; gap:1.5rem; align-items:start;">
        {{-- Left: Avatar + Stats --}}
        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            {{-- Avatar Card --}}
            <div class="glass-card" style="text-align:center;">
                <div class="avatar avatar-lg" style="margin:0 auto 0.875rem; width:72px; height:72px; font-size:1.75rem;">
                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                </div>
                <div style="font-size:1rem; font-weight:700; color:var(--text-primary);">{{ auth()->user()->name }}</div>
                <div style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.875rem;">{{ $profile->designation ?? 'Faculty' }}</div>
                <span class="pill pill-indigo">{{ strtoupper(auth()->user()->role) }}</span>
                <div style="margin-top:1rem; text-align:left;">
                    <div style="display:flex; justify-content:space-between; font-size:0.75rem; margin-bottom:0.25rem;">
                        <span style="color:var(--text-muted);">Level {{ $profile->level ?? 1 }}</span>
                        <span style="color:var(--brand-400);">{{ $profile->xp ?? 0 }} XP</span>
                    </div>
                    <div class="progress-track"><div class="progress-fill amber" style="width:{{ min(100, (($profile->xp??0)%500)/500*100) }}%;"></div></div>
                </div>
            </div>

            {{-- Performance Scores --}}
            <div class="glass-card">
                <div class="section-title" style="margin-bottom:1rem;">Performance Scores</div>
                @foreach([
                    'research_score'   => ['Research',    '#818cf8'],
                    'teaching_score'   => ['Teaching',    '#10b981'],
                    'innovation_score' => ['Innovation',  '#f59e0b'],
                ] as $field => [$label, $color])
                <div style="margin-bottom:1rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.25rem;">
                        <span style="font-size:0.8125rem; color:var(--text-secondary);">{{ $label }}</span>
                        <span style="font-size:0.8125rem; font-weight:700; color:{{ $color }};">{{ number_format(($profile->$field??0)*100,0) }}%</span>
                    </div>
                    <div class="progress-track">
                        <div style="height:100%;border-radius:99px;width:{{ min(100,($profile->$field??0)*100) }}%;background:{{ $color }};transition:width 0.8s;"></div>
                    </div>
                </div>
                @endforeach
                <div class="divider"></div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.8125rem; color:var(--text-muted);">Performance Index</span>
                    <span class="pill pill-indigo" style="font-size:0.875rem; font-weight:800;">{{ number_format(($profile->performance_index??0)*100,1) }}%</span>
                </div>
            </div>

            {{-- Skills --}}
            <div class="glass-card">
                <div class="section-title" style="margin-bottom:1rem;">Skill Tags</div>
                <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                    @forelse($profile->skills ?? [] as $skill)
                    <span class="pill pill-cyan">{{ $skill }}</span>
                    @empty
                    <span style="font-size:0.8125rem; color:var(--text-muted);">No skills added yet.</span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Edit Form --}}
        <div class="glass-card">
            <div class="section-title" style="margin-bottom:1.5rem;">Edit Profile Information</div>
            <form method="POST" action="{{ route('faculty.profile.update') }}">
                @csrf @method('PUT')
                <div class="form-grid" style="grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div class="form-group">
                        <label>Full Name <small style="color:var(--text-muted)">(manage in Account Settings)</small></label>
                        <input value="{{ auth()->user()->name }}" disabled style="opacity:0.5;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input value="{{ auth()->user()->email }}" disabled style="opacity:0.5;">
                    </div>
                    <div class="form-group">
                        <label>Designation</label>
                        <input name="designation" value="{{ old('designation', $profile->designation ?? '') }}" placeholder="e.g. Associate Professor">
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input name="department" value="{{ old('department', $profile->department ?? '') }}" placeholder="e.g. Computer Science">
                    </div>
                    <div class="form-group">
                        <label>Joining Date</label>
                        <input type="date" name="joining_date" value="{{ old('joining_date', optional($profile->joining_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label>Skills <small style="color:var(--text-muted)">(comma separated)</small></label>
                        <input name="skills" value="{{ old('skills', implode(', ', $profile->skills ?? [])) }}" placeholder="Python, Machine Learning, Pedagogy">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Professional Bio</label>
                        <textarea name="bio" rows="5" placeholder="Write a short professional bio…">{{ old('bio', $profile->bio ?? '') }}</textarea>
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
                    <a href="{{ route('faculty.report.download') }}" class="btn btn-ghost">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download Report
                    </a>
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
