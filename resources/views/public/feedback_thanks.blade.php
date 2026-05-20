<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank You – Faculty Elevate</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root { --brand-500:#6366f1; --brand-400:#818cf8; --surface-900:#0d0f1a; --surface-800:#141624; --glass-bg:rgba(28,31,53,0.8); --glass-border:rgba(129,140,248,0.18); --text-primary:#f0f2ff; --text-secondary:#9ca3c8; --text-muted:#5b6184; --accent-green:#10b981; }
html { font-family:'Inter',sans-serif; } body { background:var(--surface-900); color:var(--text-primary); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem 1rem; overflow:hidden; }
.bg-orbs { position:fixed; inset:0; pointer-events:none; } .orb { position:absolute; border-radius:50%; filter:blur(80px); opacity:0.12; animation:float 8s ease-in-out infinite; }
.orb1 { width:400px; height:400px; background:var(--accent-green); top:-100px; right:-100px; }
.orb2 { width:300px; height:300px; background:var(--brand-500); bottom:-80px; left:-80px; animation-delay:-3s; }
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-25px)} }
.card { position:relative; z-index:1; background:var(--glass-bg); backdrop-filter:blur(24px); border:1px solid var(--glass-border); border-radius:24px; padding:3rem 2.5rem; max-width:520px; width:100%; text-align:center; animation:fadeUp .6s ease; }
@keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.check-anim { width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg, var(--accent-green), #34d399); display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; animation:pop .5s cubic-bezier(0.34,1.56,0.64,1) .2s both; }
@keyframes pop { from{transform:scale(0)} to{transform:scale(1)} }
.title { font-size:1.75rem; font-weight:800; margin-bottom:.625rem; }
.sub { font-size:.95rem; color:var(--text-secondary); line-height:1.65; margin-bottom:2rem; }
.faculty-name { background:rgba(99,102,241,.12); border:1px solid var(--glass-border); border-radius:10px; padding:.625rem 1.25rem; display:inline-block; font-weight:600; color:var(--brand-400); margin-bottom:2rem; font-size:.95rem; }
.note { font-size:.78rem; color:var(--text-muted); margin-top:1.5rem; }
</style>
</head>
<body>
<div class="bg-orbs"><div class="orb orb1"></div><div class="orb orb2"></div></div>
<div class="card">
    <div class="check-anim">
        <svg width="36" height="36" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div class="title">Thank You!</div>
    <div class="sub">Your feedback has been submitted anonymously. It will help improve teaching quality and support faculty growth.</div>
    <div class="faculty-name">Feedback for {{ $faculty->name }}</div>
    <div class="note">Your identity was never recorded. Only aggregated scores are visible to faculty and staff.</div>
    <div style="margin-top:2rem; font-size:.75rem; color:var(--text-muted);">© {{ date('Y') }} Faculty Elevate</div>
</div>
</body>
</html>
