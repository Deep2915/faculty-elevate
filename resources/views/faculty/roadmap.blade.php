<x-app-layout>
    <x-slot name="header">Growth Roadmap</x-slot>

    <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem; gap:0.75rem;">
        <button onclick="document.getElementById('createGoalModal').style.display='flex'" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Goal
        </button>
    </div>

    @if($goals->isEmpty())
    <div class="glass-card" style="text-align:center; padding:4rem;">
        <div style="font-size:3rem; margin-bottom:1rem;">🗺️</div>
        <div style="font-size:1.125rem; font-weight:700; color:var(--text-primary); margin-bottom:0.5rem;">Your roadmap is empty</div>
        <div style="color:var(--text-muted); margin-bottom:1.5rem;">Start by adding professional goals and track your progress.</div>
        <button onclick="document.getElementById('createGoalModal').style.display='flex'" class="btn btn-primary">Add First Goal</button>
    </div>
    @else

    {{-- Progress Overview --}}
    @php
        $total     = $goals->count();
        $completed = $goals->where('status','completed')->count();
        $overall   = $total > 0 ? round($completed/$total*100) : 0;
    @endphp
    <div class="glass-card" style="margin-bottom:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <div>
                <div class="section-title">Overall Progress</div>
                <div style="font-size:0.8125rem; color:var(--text-muted);">{{ $completed }} of {{ $total }} goals completed</div>
            </div>
            <div style="font-size:2.5rem; font-weight:800;" class="gradient-text">{{ $overall }}%</div>
        </div>
        <div class="progress-track" style="height:10px;">
            <div class="progress-fill green" style="width:{{ $overall }}%;"></div>
        </div>
    </div>

    {{-- Goals Timeline --}}
    <div style="position:relative; padding-left:2rem;">
        {{-- Vertical Line --}}
        <div style="position:absolute; left:14px; top:0; bottom:0; width:2px; background:var(--glass-border);"></div>

        @foreach($goals as $goal)
        @php
            $pct = (float)($goal->completion_percentage ?? 0);
            $isDone = $goal->status === 'completed';
            $isOver = $goal->status === 'active' && \Carbon\Carbon::parse($goal->target_date)->isPast();
        @endphp
        <div style="position:relative; margin-bottom:1.5rem;">
            {{-- Dot --}}
            <div style="position:absolute; left:-2rem; top:1.25rem; width:16px; height:16px; border-radius:50%; border:2px solid {{ $isDone ? '#10b981' : ($isOver ? '#f43f5e' : '#6366f1') }}; background:var(--surface-800); z-index:2;"></div>

            <div class="glass-card" style="padding:1.25rem;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.875rem; gap:1rem;">
                    <div style="flex:1;">
                        <div style="font-weight:700; color:var(--text-primary); font-size:0.9375rem; margin-bottom:0.25rem;">{{ $goal->title }}</div>
                        <div style="font-size:0.8125rem; color:var(--text-muted);">{{ $goal->description }}</div>
                    </div>
                    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:0.375rem; flex-shrink:0;">
                        <span class="pill {{ $isDone?'pill-green':($isOver?'pill-rose':'pill-indigo') }}">
                            {{ $isDone ? '✅ Completed' : ($isOver ? '⏰ Overdue' : '🔄 Active') }}
                        </span>
                        <span style="font-size:0.72rem; color:var(--text-muted);">Due: {{ \Carbon\Carbon::parse($goal->target_date)->format('d M Y') }}</span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div style="margin-bottom:0.75rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.375rem;">
                        <span style="font-size:0.72rem; color:var(--text-muted);">Completion</span>
                        <span style="font-size:0.72rem; font-weight:700; color:var(--brand-400);">{{ $pct }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill {{ $isDone?'green':'' }}" style="width:{{ min(100,$pct) }}%;"></div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    @foreach([0,25,50,75,100] as $step)
                    <form method="POST" action="{{ route('faculty.goals.update', $goal->id) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="completion_percentage" value="{{ $step }}">
                        <input type="hidden" name="status" value="{{ $step===100?'completed':'active' }}">
                        <button class="btn btn-ghost btn-xs {{ $pct==$step?'btn-primary':'' }}">{{ $step }}%</button>
                    </form>
                    @endforeach
                    <form method="POST" action="{{ route('faculty.goals.destroy', $goal->id) }}" onsubmit="return confirm('Delete goal?')" style="margin-left:auto;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-xs">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- CREATE GOAL MODAL --}}
    <div id="createGoalModal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
        <div class="modal-box">
            <div class="modal-title">🎯 New Professional Goal</div>
            <form method="POST" action="{{ route('faculty.goals.store') }}">
                @csrf
                <div style="display:flex; flex-direction:column; gap:0.875rem; margin-bottom:1.25rem;">
                    <div class="form-group"><label>Goal Title</label><input name="title" placeholder="e.g. Publish IEEE paper by Q3" required></div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="2" placeholder="Describe your goal and what success looks like…" required></textarea></div>
                    <div class="form-group"><label>Target Date</label><input type="date" name="target_date" required></div>
                </div>
                <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('createGoalModal').style.display='none'" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Goal</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
