<x-app-layout>
    <x-slot name="header">Wellbeing Check-In</x-slot>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
        {{-- Survey Form --}}
        <div class="glass-card">
            <div class="section-header" style="margin-bottom:1.5rem;">
                <span class="section-title">❤️ How are you feeling?</span>
                <span class="pill pill-rose">Survey</span>
            </div>
            <form method="POST" action="{{ route('faculty.wellbeing.store') }}" id="wellbeingForm">
                @csrf
                @php
                    $sliders = [
                        'workload'   => ['label'=>'Workload', 'desc'=>'How manageable is your current workload?', 'icon'=>'📋'],
                        'stress'     => ['label'=>'Stress Level', 'desc'=>'Rate your current stress level (1=low, 10=high)', 'icon'=>'😓'],
                        'motivation' => ['label'=>'Motivation', 'desc'=>'How motivated do you feel about your work?', 'icon'=>'⚡'],
                        'support'    => ['label'=>'Support', 'desc'=>'Do you feel supported by your institution?', 'icon'=>'🤝'],
                    ];
                @endphp

                @foreach($sliders as $key => $s)
                <div style="margin-bottom:1.5rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.375rem;">
                        <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; color:var(--text-primary); font-weight:600;">
                            {{ $s['icon'] }} {{ $s['label'] }}
                        </label>
                        <span id="{{ $key }}Val" style="font-weight:800; color:var(--brand-400); font-size:1rem;">5</span>
                    </div>
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.5rem;">{{ $s['desc'] }}</div>
                    <input type="range" name="responses[{{ $key }}]" id="{{ $key }}Slider" min="1" max="10" value="5"
                           oninput="document.getElementById('{{ $key }}Val').textContent=this.value; updateBurnout()">
                    <div style="display:flex; justify-content:space-between; font-size:0.65rem; color:var(--text-muted); margin-top:4px;">
                        <span>1 – Low</span><span>10 – High</span>
                    </div>
                </div>
                @endforeach

                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label>Additional Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Anything you'd like to share with your HOD…"></textarea>
                </div>

                {{-- Burnout Indicator --}}
                <div id="burnoutBox" style="border-radius:10px; padding:0.875rem; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); margin-bottom:1.25rem; text-align:center;">
                    <div style="font-size:0.8125rem; color:var(--text-muted);">Estimated Wellbeing Score</div>
                    <div id="burnoutVal" style="font-size:1.75rem; font-weight:800; color:#10b981;">75%</div>
                    <div id="burnoutLabel" style="font-size:0.75rem; color:var(--text-muted);">Looking Good! 🌟</div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Submit Check-In</button>
            </form>
        </div>

        {{-- Trend Chart --}}
        <div class="glass-card">
            <div class="section-header" style="margin-bottom:1.25rem;">
                <span class="section-title">📊 Wellbeing Trend</span>
                <span class="pill pill-indigo">Last 10</span>
            </div>
            @if($surveys->isNotEmpty())
            <div class="chart-container" style="height:260px;"><canvas id="trendChart"></canvas></div>
            <div class="divider"></div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                @php $latest = $surveys->last(); @endphp
                <div style="background:var(--surface-700); border-radius:10px; padding:0.875rem; text-align:center;">
                    <div style="font-size:0.72rem; color:var(--text-muted);">Latest Burnout Index</div>
                    <div style="font-size:1.5rem; font-weight:800; color:{{ ($latest->burnout_index??50)>50?'#10b981':'#f43f5e' }};">{{ number_format($latest->burnout_index??0,1) }}%</div>
                </div>
                <div style="background:var(--surface-700); border-radius:10px; padding:0.875rem; text-align:center;">
                    <div style="font-size:0.72rem; color:var(--text-muted);">Check-ins Completed</div>
                    <div style="font-size:1.5rem; font-weight:800; color:var(--brand-400);">{{ $surveys->count() }}</div>
                </div>
            </div>
            @else
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                <div style="font-size:2.5rem; margin-bottom:0.75rem;">📊</div>
                Complete your first check-in to see trends here.
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    function updateBurnout() {
        const w = parseInt(document.getElementById('workloadSlider').value);
        const s = parseInt(document.getElementById('stressSlider').value);
        const m = parseInt(document.getElementById('motivationSlider').value);
        const sup = parseInt(document.getElementById('supportSlider').value);
        // High stress/workload = high burnout risk; high motivation/support = low burnout risk
        const wellbeing = ((11-w) + (11-s) + m + sup) / 4 / 10 * 100;
        document.getElementById('burnoutVal').textContent = Math.round(wellbeing) + '%';
        const box = document.getElementById('burnoutBox');
        const lbl = document.getElementById('burnoutLabel');
        const val = document.getElementById('burnoutVal');
        if (wellbeing >= 70) {
            box.style.background='rgba(16,185,129,0.1)'; box.style.border='1px solid rgba(16,185,129,0.3)';
            val.style.color='#10b981'; lbl.textContent='Looking Good! 🌟';
        } else if (wellbeing >= 40) {
            box.style.background='rgba(245,158,11,0.1)'; box.style.border='1px solid rgba(245,158,11,0.3)';
            val.style.color='#f59e0b'; lbl.textContent='Moderate stress — keep an eye out ⚠️';
        } else {
            box.style.background='rgba(244,63,94,0.1)'; box.style.border='1px solid rgba(244,63,94,0.3)';
            val.style.color='#f43f5e'; lbl.textContent='High burnout risk — please seek support 🚨';
        }
    }
    updateBurnout();

    @if($surveys->isNotEmpty())
    @php
        $surveyLabels = $surveys->map(function($s){ return optional($s->surveyed_at)->format('d M'); });
        $surveyData   = $surveys->pluck('burnout_index');
    @endphp
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {{ json_encode($surveyLabels) }},
            datasets: [{
                label: 'Wellbeing Score',
                data: {{ json_encode($surveyData) }},
                borderColor: '#818cf8',
                backgroundColor: 'rgba(99,102,241,0.08)',
                tension: 0.4, fill: true, pointRadius: 5,
                pointBackgroundColor: '#818cf8',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { min: 0, max: 100, grid: { color: 'rgba(255,255,255,0.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
    @endif
    </script>
    @endpush
</x-app-layout>
