<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Faculty Elevate') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #080b18;
            min-height: 100vh;
            overflow-x: hidden;
            color: #f0f2ff;
        }

        /* ── Animated Background ── */
        .auth-bg {
            position: fixed; inset: 0; z-index: 0;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(99,102,241,0.18) 0%, transparent 60%),
                        radial-gradient(ellipse 60% 40% at 80% 80%, rgba(168,85,247,0.12) 0%, transparent 50%),
                        radial-gradient(ellipse 50% 30% at 10% 70%, rgba(6,182,212,0.1) 0%, transparent 50%),
                        #080b18;
        }

        /* ── Floating Orbs ── */
        .orb {
            position: fixed; border-radius: 50%; filter: blur(80px);
            animation: orbFloat 12s ease-in-out infinite; z-index: 0;
        }
        .orb-1 { width: 500px; height: 500px; top: -200px; left: -150px; background: rgba(99,102,241,0.12); animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; bottom: -150px; right: -100px; background: rgba(168,85,247,0.1); animation-delay: -4s; }
        .orb-3 { width: 300px; height: 300px; top: 50%; left: 60%; background: rgba(6,182,212,0.08); animation-delay: -8s; }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -30px) scale(1.05); }
            66%       { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* ── Grid Overlay ── */
        .grid-overlay {
            position: fixed; inset: 0; z-index: 0;
            background-image: linear-gradient(rgba(129,140,248,0.04) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(129,140,248,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── Particles ── */
        .particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .particle {
            position: absolute; width: 2px; height: 2px;
            border-radius: 50%; background: rgba(129,140,248,0.6);
            animation: particleRise linear infinite;
        }

        @keyframes particleRise {
            0%   { transform: translateY(0) translateX(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.5; }
            100% { transform: translateY(-100vh) translateX(40px); opacity: 0; }
        }

        /* ── Auth Wrapper ── */
        .auth-wrapper {
            position: relative; z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ── Branding Panel ── */
        .brand-panel {
            display: none;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            max-width: 460px;
            animation: slideInLeft 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @media (min-width: 1024px) { .brand-panel { display: flex; } }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .brand-logo {
            display: flex; align-items: center; gap: 0.875rem; margin-bottom: 3rem;
        }
        .brand-logo-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 32px rgba(99,102,241,0.4);
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(99,102,241,0.4); }
            50%       { box-shadow: 0 8px 48px rgba(99,102,241,0.7); }
        }
        .brand-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem; font-weight: 700;
            background: linear-gradient(135deg, #f0f2ff, #a5b4fc);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-tagline { font-size: 0.75rem; color: #5b6184; letter-spacing: 0.08em; text-transform: uppercase; }

        .brand-headline {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2.75rem; font-weight: 700; line-height: 1.15;
            color: #f0f2ff; margin-bottom: 1.25rem;
        }
        .brand-headline span {
            background: linear-gradient(135deg, #818cf8, #c084fc);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-desc { font-size: 1rem; color: #6b7280; line-height: 1.7; margin-bottom: 2.5rem; }

        .feature-list { display: flex; flex-direction: column; gap: 0.875rem; }
        .feature-item {
            display: flex; align-items: center; gap: 0.75rem;
            animation: fadeSlideIn 0.5s ease both;
        }
        .feature-item:nth-child(1) { animation-delay: 0.3s; }
        .feature-item:nth-child(2) { animation-delay: 0.45s; }
        .feature-item:nth-child(3) { animation-delay: 0.6s; }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .feature-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-text { font-size: 0.875rem; color: #9ca3c8; }
        .feature-text strong { color: #e0e7ff; font-weight: 600; }

        /* ── Auth Card ── */
        .auth-card {
            width: 100%; max-width: 460px;
            background: rgba(20, 22, 36, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(129,140,248,0.15);
            border-radius: 24px;
            padding: 2.5rem;
            animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
            box-shadow: 0 24px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05);
        }
        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .auth-card-header { text-align: center; margin-bottom: 2rem; }
        .auth-card-icon {
            width: 64px; height: 64px; border-radius: 18px; margin: 0 auto 1.25rem;
            background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(168,85,247,0.2));
            border: 1px solid rgba(129,140,248,0.3);
            display: flex; align-items: center; justify-content: center;
            animation: iconBounce 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.2s both;
        }
        @keyframes iconBounce {
            from { opacity: 0; transform: scale(0.5) rotate(-10deg); }
            to   { opacity: 1; transform: scale(1) rotate(0); }
        }
        .auth-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.625rem; font-weight: 700; color: #f0f2ff;
            margin-bottom: 0.375rem;
        }
        .auth-subtitle { font-size: 0.875rem; color: #5b6184; }
        .auth-subtitle a { color: #818cf8; font-weight: 500; transition: color 0.2s; }
        .auth-subtitle a:hover { color: #a5b4fc; }

        /* ── Form Fields ── */
        .field-group { margin-bottom: 1.125rem; }
        .field-label {
            display: block; font-size: 0.8125rem; font-weight: 500;
            color: #9ca3c8; margin-bottom: 0.5rem; letter-spacing: 0.01em;
        }
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%);
            color: #5b6184; pointer-events: none; transition: color 0.2s;
        }
        .field-input {
            width: 100%; padding: 0.75rem 0.875rem 0.75rem 2.75rem;
            background: rgba(28, 31, 53, 0.8);
            border: 1px solid rgba(129,140,248,0.12);
            border-radius: 12px;
            color: #f0f2ff; font-size: 0.9rem; font-family: 'Inter', sans-serif;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            outline: none;
        }
        .field-input::placeholder { color: #374163; }
        .field-input:focus {
            border-color: rgba(99,102,241,0.6);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
            background: rgba(35, 38, 65, 0.9);
        }
        .field-wrap:focus-within .field-icon { color: #818cf8; }

        .field-input-noicon {
            padding-left: 0.875rem;
        }

        /* Password toggle */
        .pwd-toggle {
            position: absolute; right: 0.875rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #5b6184; transition: color 0.2s; padding: 0;
        }
        .pwd-toggle:hover { color: #818cf8; }

        /* ── Error Messages ── */
        .field-error { font-size: 0.75rem; color: #fb7185; margin-top: 0.375rem; display: flex; align-items: center; gap: 0.25rem; }

        /* ── Checkbox ── */
        .check-label {
            display: flex; align-items: center; gap: 0.625rem;
            cursor: pointer; font-size: 0.8125rem; color: #9ca3c8;
        }
        .check-label input[type="checkbox"] {
            width: 16px; height: 16px; border-radius: 4px; padding: 0;
            border: 1px solid rgba(129,140,248,0.3);
            background: rgba(28,31,53,0.8); cursor: pointer; flex-shrink: 0;
            accent-color: #6366f1;
        }

        /* ── Submit Button ── */
        .btn-auth {
            width: 100%; padding: 0.875rem;
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            border: none; border-radius: 12px;
            color: #fff; font-size: 0.9375rem; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer; position: relative; overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 6px 24px rgba(99,102,241,0.4);
            letter-spacing: 0.02em;
        }
        .btn-auth::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0; transition: opacity 0.2s;
        }
        .btn-auth:hover { transform: translateY(-2px); box-shadow: 0 10px 36px rgba(99,102,241,0.55); }
        .btn-auth:hover::before { opacity: 1; }
        .btn-auth:active { transform: translateY(0); }

        /* Ripple */
        .btn-auth .ripple {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: scale(0); animation: ripple 0.6s linear;
            pointer-events: none;
        }
        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }

        /* ── Divider ── */
        .auth-divider {
            display: flex; align-items: center; gap: 1rem;
            margin: 1.5rem 0; color: #374163; font-size: 0.75rem;
        }
        .auth-divider::before, .auth-divider::after {
            content: ''; flex: 1; height: 1px; background: rgba(129,140,248,0.1);
        }

        /* ── Flash ── */
        .auth-flash {
            padding: 0.75rem 1rem; border-radius: 10px;
            font-size: 0.8125rem; margin-bottom: 1.25rem;
            display: flex; align-items: flex-start; gap: 0.5rem;
            animation: flashIn 0.3s ease;
        }
        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .auth-flash.success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #6ee7b7; }
        .auth-flash.error   { background: rgba(244,63,94,0.1); border: 1px solid rgba(244,63,94,0.25); color: #fda4af; }

        /* ── Password Strength ── */
        .pwd-strength { margin-top: 0.5rem; }
        .pwd-bars { display: flex; gap: 4px; margin-bottom: 0.25rem; }
        .pwd-bar {
            flex: 1; height: 3px; border-radius: 99px;
            background: rgba(129,140,248,0.1); transition: background 0.3s;
        }
        .pwd-bar.weak   { background: #f43f5e; }
        .pwd-bar.fair   { background: #f59e0b; }
        .pwd-bar.good   { background: #10b981; }
        .pwd-bar.strong { background: #6366f1; }
        .pwd-strength-label { font-size: 0.7rem; color: #5b6184; }

        /* ── Floating Label ── */
        .auth-form { animation: formIn 0.5s ease 0.1s both; }
        @keyframes formIn {
            from { opacity: 0; } to { opacity: 1; }
        }

        /* ── Role Selector ── */
        .role-selector { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.625rem; margin-bottom: 1.125rem; }
        .role-option { display: none; }
        .role-label {
            display: flex; flex-direction: column; align-items: center; gap: 0.4rem;
            padding: 0.75rem 0.5rem; border-radius: 12px;
            border: 1px solid rgba(129,140,248,0.12);
            background: rgba(28,31,53,0.6);
            cursor: pointer; transition: all 0.2s;
            font-size: 0.75rem; color: #5b6184; font-weight: 500;
        }
        .role-label svg { opacity: 0.5; transition: opacity 0.2s; }
        .role-option:checked + .role-label {
            border-color: rgba(99,102,241,0.5);
            background: rgba(99,102,241,0.12);
            color: #a5b4fc;
        }
        .role-option:checked + .role-label svg { opacity: 1; }
        .role-label:hover { border-color: rgba(129,140,248,0.3); color: #9ca3c8; }

        /* ── Stats strip ── */
        .stats-strip {
            display: flex; gap: 2rem; margin-top: 3rem;
            padding-top: 2rem; border-top: 1px solid rgba(129,140,248,0.1);
        }
        .stat-item { }
        .stat-val {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem; font-weight: 700; color: #f0f2ff;
        }
        .stat-lbl { font-size: 0.75rem; color: #5b6184; margin-top: 0.125rem; }
    </style>
</head>
<body>
    <!-- Background -->
    <div class="auth-bg"></div>
    <div class="grid-overlay"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <!-- Main Wrapper -->
    <div class="auth-wrapper">
        <!-- Brand Panel (desktop only) -->
        <div class="brand-panel" style="flex:1; padding-right:4rem;">
            <div class="brand-logo">
                <div class="brand-logo-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-name">Faculty Elevate</div>
                    <div class="brand-tagline">Performance Excellence Platform</div>
                </div>
            </div>

            <h1 class="brand-headline">Elevate Your<br><span>Academic Career</span></h1>
            <p class="brand-desc">The all-in-one performance management platform built for modern academic institutions. Track, analyze, and grow.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon" style="background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.2);">
                        <svg width="18" height="18" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                    </div>
                    <div class="feature-text"><strong>Real-time Analytics</strong> — Live performance dashboards</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon" style="background:rgba(168,85,247,0.15); border:1px solid rgba(168,85,247,0.2);">
                        <svg width="18" height="18" fill="none" stroke="#c084fc" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div class="feature-text"><strong>Gamification</strong> — Badges, ranks & leaderboards</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon" style="background:rgba(6,182,212,0.15); border:1px solid rgba(6,182,212,0.2);">
                        <svg width="18" height="18" fill="none" stroke="#22d3ee" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <div class="feature-text"><strong>Smart Evaluations</strong> — HOD-driven insights</div>
                </div>
            </div>

            <div class="stats-strip">
                <div class="stat-item"><div class="stat-val">500+</div><div class="stat-lbl">Faculty Members</div></div>
                <div class="stat-item"><div class="stat-val">98%</div><div class="stat-lbl">Satisfaction Rate</div></div>
                <div class="stat-item"><div class="stat-val">12K+</div><div class="stat-lbl">Goals Achieved</div></div>
            </div>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>

    <script>
        // Generate particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 25; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.cssText = `
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                width: ${Math.random() * 2 + 1}px;
                height: ${Math.random() * 2 + 1}px;
                animation-duration: ${Math.random() * 15 + 10}s;
                animation-delay: ${Math.random() * -20}s;
                opacity: ${Math.random() * 0.6 + 0.2};
            `;
            container.appendChild(p);
        }

        // Ripple on button click
        document.querySelectorAll('.btn-auth').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left, y = e.clientY - rect.top;
                const r = document.createElement('span');
                r.className = 'ripple';
                r.style.cssText = `width:60px;height:60px;left:${x-30}px;top:${y-30}px;`;
                this.appendChild(r);
                setTimeout(() => r.remove(), 600);
            });
        });

        // Password toggle
        document.querySelectorAll('.pwd-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.previousElementSibling || this.parentElement.querySelector('input');
                const isText = input.type === 'text';
                input.type = isText ? 'password' : 'text';
                this.innerHTML = isText
                    ? `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`
                    : `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
            });
        });

        // Password strength meter
        const pwdInput = document.getElementById('password');
        if (pwdInput && document.getElementById('pwd-bars')) {
            pwdInput.addEventListener('input', function() {
                const val = this.value;
                const bars = document.querySelectorAll('.pwd-bar');
                const label = document.getElementById('pwd-strength-label');
                let score = 0;
                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;

                const levels = ['', 'weak', 'fair', 'good', 'strong'];
                const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
                bars.forEach((b, i) => {
                    b.className = 'pwd-bar';
                    if (i < score) b.classList.add(levels[score]);
                });
                if (label) label.textContent = val.length ? labels[score] : '';
            });
        }
    </script>
</body>
</html>
