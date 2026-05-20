<x-app-layout>
    <x-slot name="header">Score Weights Configuration</x-slot>

    <div style="max-width:640px; margin:0 auto;">
        <div class="glass-card">
            <div class="section-header" style="margin-bottom:1.75rem;">
                <div>
                    <div class="section-title">Performance Index Weights</div>
                    <div style="font-size:0.8125rem; color:var(--text-muted); margin-top:0.25rem;">Adjust how each category contributes to the faculty Performance Index. Total must equal 100%.</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.weights.update') }}" id="weightForm">
                @csrf @method('PUT')

                @php
                    $weights = $weights ?? ['research'=>40,'teaching'=>40,'innovation'=>20];
                    $r = $weights['research']   ?? 40;
                    $t = $weights['teaching']   ?? 40;
                    $in= $weights['innovation'] ?? 20;
                @endphp

                {{-- Research --}}
                <div style="margin-bottom:1.75rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.9rem; color:var(--text-primary);">
                            <span style="width:10px;height:10px;border-radius:50%;background:#818cf8;display:inline-block;"></span>
                            Research Score
                        </label>
                        <span id="resVal" style="font-weight:700; color:var(--brand-400);">{{ $r }}%</span>
                    </div>
                    <input type="range" name="weights[research]" id="resSlider" min="0" max="100" value="{{ $r }}" oninput="updateWeights()">
                </div>

                {{-- Teaching --}}
                <div style="margin-bottom:1.75rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.9rem; color:var(--text-primary);">
                            <span style="width:10px;height:10px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                            Teaching Score
                        </label>
                        <span id="teachVal" style="font-weight:700; color:var(--accent-green);">{{ $t }}%</span>
                    </div>
                    <input type="range" name="weights[teaching]" id="teachSlider" min="0" max="100" value="{{ $t }}" oninput="updateWeights()"
                           style="background: linear-gradient(90deg, #10b981, #34d399);">
                </div>

                {{-- Innovation --}}
                <div style="margin-bottom:1.75rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                        <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.9rem; color:var(--text-primary);">
                            <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                            Innovation Score
                        </label>
                        <span id="innovVal" style="font-weight:700; color:var(--accent-amber);">{{ $in }}%</span>
                    </div>
                    <input type="range" name="weights[innovation]" id="innovSlider" min="0" max="100" value="{{ $in }}" oninput="updateWeights()"
                           style="background: linear-gradient(90deg, #f59e0b, #fbbf24);">
                </div>

                {{-- Total Indicator --}}
                <div id="totalBox" style="border-radius:10px; padding:0.875rem 1.25rem; background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.875rem; color:var(--text-secondary);">Total Weight</span>
                    <span id="totalVal" style="font-size:1.25rem; font-weight:800; color:var(--brand-400);">{{ $r+$t+$in }}%</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;" id="saveBtn">Save Weight Configuration</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function updateWeights() {
        const r = parseInt(document.getElementById('resSlider').value);
        const t = parseInt(document.getElementById('teachSlider').value);
        const i = parseInt(document.getElementById('innovSlider').value);
        const total = r + t + i;

        document.getElementById('resVal').textContent   = r + '%';
        document.getElementById('teachVal').textContent = t + '%';
        document.getElementById('innovVal').textContent = i + '%';
        document.getElementById('totalVal').textContent = total + '%';

        const box  = document.getElementById('totalBox');
        const btn  = document.getElementById('saveBtn');
        const good = total === 100;
        box.style.background = good ? 'rgba(16,185,129,0.1)'   : 'rgba(244,63,94,0.1)';
        box.style.border     = good ? '1px solid rgba(16,185,129,0.3)' : '1px solid rgba(244,63,94,0.3)';
        document.getElementById('totalVal').style.color = good ? '#10b981' : '#f43f5e';
        btn.disabled = !good;
        btn.style.opacity = good ? '1' : '0.5';
    }
    updateWeights();
    document.getElementById('weightForm').addEventListener('submit', function(e) {
        const total = parseInt(document.getElementById('resSlider').value)
                    + parseInt(document.getElementById('teachSlider').value)
                    + parseInt(document.getElementById('innovSlider').value);
        if (total !== 100) { e.preventDefault(); alert('Weights must total exactly 100%.'); }
    });
    </script>
    @endpush
</x-app-layout>
