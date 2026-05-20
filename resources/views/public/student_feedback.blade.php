<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Feedback – Faculty Elevate</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --brand-500: #6366f1; --brand-400: #818cf8; --brand-600: #4f46e5;
    --surface-900: #0d0f1a; --surface-800: #141624; --surface-700: #1c1f35;
    --glass-bg: rgba(28,31,53,0.8); --glass-border: rgba(129,140,248,0.18);
    --text-primary: #f0f2ff; --text-secondary: #9ca3c8; --text-muted: #5b6184;
    --accent-green: #10b981; --accent-amber: #f59e0b; --accent-rose: #f43f5e;
}
html { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
body { background: var(--surface-900); color: var(--text-primary); min-height: 100vh; overflow-x: hidden; -webkit-font-smoothing: antialiased; }
::-webkit-scrollbar { width: 4px; } ::-webkit-scrollbar-track { background: var(--surface-800); } ::-webkit-scrollbar-thumb { background: var(--brand-500); border-radius: 99px; }

/* Animated background */
.bg-orbs { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
.orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.15; animation: float 8s ease-in-out infinite; }
.orb1 { width: 400px; height: 400px; background: var(--brand-500); top: -100px; right: -100px; animation-delay: 0s; }
.orb2 { width: 300px; height: 300px; background: #a855f7; bottom: -80px; left: -80px; animation-delay: -3s; }
.orb3 { width: 200px; height: 200px; background: #06b6d4; top: 50%; left: 40%; animation-delay: -5s; }
@keyframes float { 0%, 100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-30px) scale(1.05); } }

.wrapper { position: relative; z-index: 1; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 1rem; }

/* Header */
.header { text-align: center; margin-bottom: 2.5rem; animation: fadeUp .6s ease; }
.header-logo { display: inline-flex; align-items: center; gap: .75rem; margin-bottom: 1.5rem; }
.header-logo-icon { width: 48px; height: 48px; background: linear-gradient(135deg, var(--brand-500), #a855f7); border-radius: 14px; display: flex; align-items: center; justify-content: center; }
.header-logo-text { font-size: 1.125rem; font-weight: 700; color: var(--text-primary); }
.header-title { font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, var(--brand-400), #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: .75rem; }
.header-sub { font-size: .95rem; color: var(--text-secondary); }
.faculty-badge { display: inline-flex; align-items: center; gap: .5rem; background: var(--glass-bg); border: 1px solid var(--glass-border); backdrop-filter: blur(12px); border-radius: 99px; padding: .5rem 1.25rem; margin-top: 1rem; font-size: .85rem; }
.faculty-badge .avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, var(--brand-500), #a855f7); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .75rem; }

/* Card */
.card { background: var(--glass-bg); backdrop-filter: blur(24px); border: 1px solid var(--glass-border); border-radius: 24px; padding: 2.5rem; width: 100%; max-width: 640px; animation: fadeUp .7s ease .1s both; }
.card-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; }
.card-sub { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 2rem; }

/* Star Rating */
.rating-section { margin-bottom: 1.75rem; }
.rating-label { font-size: .875rem; font-weight: 600; color: var(--text-secondary); margin-bottom: .625rem; display: flex; align-items: center; gap: .5rem; }
.stars { display: flex; gap: .375rem; flex-direction: row-reverse; justify-content: flex-end; }
.stars input { display: none; }
.stars label { cursor: pointer; font-size: 2rem; color: var(--text-muted); transition: color .15s, transform .15s; line-height: 1; }
.stars label:hover, .stars label:hover ~ label,
.stars input:checked ~ label { color: #fbbf24; }
.stars label:hover { transform: scale(1.2); }
.rating-val { font-size: .75rem; color: var(--text-muted); margin-top: .375rem; height: 1em; transition: color .2s; }

/* Textarea */
.form-group { margin-bottom: 1.5rem; }
.form-label { font-size: .8125rem; font-weight: 500; color: var(--text-secondary); margin-bottom: .375rem; display: block; }
textarea { width: 100%; background: var(--surface-700); color: var(--text-primary); border: 1px solid var(--glass-border); border-radius: 12px; padding: .875rem 1rem; font-family: inherit; font-size: .875rem; resize: vertical; transition: border-color .2s, box-shadow .2s; }
textarea:focus { outline: none; border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
textarea::placeholder { color: var(--text-muted); }

/* Submit button */
.btn-submit { width: 100%; padding: .875rem; background: linear-gradient(135deg, var(--brand-500), var(--brand-600)); color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: transform .18s, box-shadow .18s; box-shadow: 0 4px 20px rgba(99,102,241,.4); display: flex; align-items: center; justify-content: center; gap: .5rem; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(99,102,241,.55); }
.btn-submit:active { transform: translateY(0); }

/* Anon note */
.anon-note { display: flex; align-items: flex-start; gap: .625rem; background: rgba(99,102,241,.08); border: 1px solid rgba(99,102,241,.18); border-radius: 10px; padding: .75rem 1rem; margin-bottom: 1.75rem; font-size: .78rem; color: var(--text-secondary); line-height: 1.6; }

/* Errors */
.error-box { background: rgba(244,63,94,.1); border: 1px solid rgba(244,63,94,.3); border-radius: 10px; padding: .75rem 1rem; margin-bottom: 1.5rem; font-size: .8rem; color: #fda4af; }
.error-box ul { padding-left: 1.25rem; }

/* Divider */
.divider { height: 1px; background: var(--glass-border); margin: 1.5rem 0; }

@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 640px) { .card { padding: 1.5rem; border-radius: 16px; } .header-title { font-size: 1.5rem; } }
</style>
</head>
<body>
<div class="bg-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="wrapper">
    <div class="header">
        <div class="header-logo">
            <div class="header-logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <span class="header-logo-text">Faculty Elevate</span>
        </div>
        <div class="header-title">Share Your Feedback</div>
        <div class="header-sub">Help improve teaching quality with your honest, anonymous review</div>
        <div class="faculty-badge">
            <div class="avatar">{{ strtoupper(substr($faculty->name, 0, 1)) }}</div>
            <span style="font-weight:600; color:var(--text-primary);">{{ $faculty->name }}</span>
            <span style="color:var(--text-muted);">· Faculty</span>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Student Feedback Form</div>
        <div class="card-sub">Rate your experience across four key areas. All submissions are 100% anonymous.</div>

        <div class="anon-note">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Your identity is never recorded. No login required. Only aggregated scores are shared with the faculty.</span>
        </div>

        @if($errors->any())
        <div class="error-box"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('feedback.store', $token) }}" id="feedbackForm">
            @csrf

            {{-- Clarity --}}
            @foreach([
                ['key'=>'clarity',       'label'=>'Teaching Clarity',      'desc'=>'How clearly does the faculty explain concepts?'],
                ['key'=>'communication', 'label'=>'Communication',          'desc'=>'How effective is the faculty\'s communication style?'],
                ['key'=>'punctuality',   'label'=>'Punctuality',             'desc'=>'Does the faculty start/end sessions on time?'],
                ['key'=>'engagement',    'label'=>'Student Engagement',     'desc'=>'How engaging and interactive are the sessions?'],
            ] as $dim)
            <div class="rating-section">
                <div class="rating-label">
                    {{ $dim['label'] }}
                    <span style="font-size:.72rem; font-weight:400; color:var(--text-muted);">— {{ $dim['desc'] }}</span>
                </div>
                <div class="stars" id="stars-{{ $dim['key'] }}">
                    @for($s = 5; $s >= 1; $s--)
                    <input type="radio" id="star-{{ $dim['key'] }}-{{ $s }}" name="star_{{ $dim['key'] }}" value="{{ $s }}" required>
                    <label for="star-{{ $dim['key'] }}-{{ $s }}" title="{{ $s }} star{{ $s > 1 ? 's' : '' }}">★</label>
                    @endfor
                    <input type="hidden" name="scores[{{ $dim['key'] }}]" id="score-{{ $dim['key'] }}" value="">
                </div>
                <div class="rating-val" id="val-{{ $dim['key'] }}">Click to rate</div>
            </div>
            @endforeach

            <div class="divider"></div>

            <div class="form-group">
                <label class="form-label">Additional Comments <span style="color:var(--text-muted); font-weight:400;">(optional)</span></label>
                <textarea name="comment" rows="4" placeholder="Share any specific observations, suggestions, or praise…">{{ old('comment') }}</textarea>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Submit Anonymous Feedback
            </button>
        </form>
    </div>

    <div style="margin-top:2rem; text-align:center; font-size:.75rem; color:var(--text-muted);">
        © {{ date('Y') }} Faculty Elevate · Academic Performance Platform
    </div>
</div>

<script>
const labels = { 1: 'Poor', 2: 'Fair', 3: 'Good', 4: 'Very Good', 5: 'Excellent' };
const dims = ['clarity', 'communication', 'punctuality', 'engagement'];

dims.forEach(dim => {
    const radios = document.querySelectorAll(`input[name="star_${dim}"]`);
    const valEl  = document.getElementById(`val-${dim}`);
    const hidden = document.getElementById(`score-${dim}`);

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            const v = parseInt(radio.value);
            hidden.value = (v / 5).toFixed(2);
            valEl.textContent = `${labels[v]} (${v}/5)`;
            valEl.style.color = v >= 4 ? 'var(--accent-green)' : v >= 3 ? 'var(--accent-amber)' : 'var(--accent-rose)';
        });
    });
});

document.getElementById('feedbackForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.textContent = 'Submitting…';
    btn.disabled = true;
});
</script>
</body>
</html>
