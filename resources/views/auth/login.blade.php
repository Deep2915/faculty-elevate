<x-guest-layout>
    <!-- Header -->
    <div class="auth-card-header">
        <div class="auth-card-icon">
            <svg width="28" height="28" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
        </div>
        <h2 class="auth-title">Welcome Back</h2>
        <p class="auth-subtitle">
            Don't have an account? <a href="{{ route('register') }}">Create one free</a>
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="auth-flash success">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="auth-flash error">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm">
        @csrf

        <!-- Email -->
        <div class="field-group">
            <label class="field-label" for="email">Email Address</label>
            <div class="field-wrap">
                <span class="field-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
                <input
                    id="email" name="email" type="email"
                    class="field-input" placeholder="you@institution.edu"
                    value="{{ old('email') }}" required autofocus autocomplete="username"
                >
            </div>
            @error('email')
                <div class="field-error">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="field-group">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                <label class="field-label" for="password" style="margin-bottom:0;">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size:0.75rem;color:#6366f1;font-weight:500;transition:color 0.2s;"
                       onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#6366f1'">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="field-wrap">
                <span class="field-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <input
                    id="password" name="password" type="password"
                    class="field-input" placeholder="Enter your password"
                    required autocomplete="current-password"
                    style="padding-right:2.75rem;"
                >
                <button type="button" class="pwd-toggle" id="pwd-toggle-login" title="Toggle password visibility">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="field-error">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <label class="check-label" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me for 30 days</span>
            </label>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-auth" id="loginBtn">
            <span id="loginBtnText" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Sign In to Faculty Elevate
            </span>
        </button>
    </form>

    <div class="auth-divider">or continue as</div>

    <!-- Role Quick-links -->
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.625rem;">
        @php $roles = [['Admin','#6366f1'],['HOD','#a855f7'],['Faculty','#06b6d4']]; @endphp
        @foreach($roles as [$role, $color])
        <div style="padding:0.625rem 0.5rem;border-radius:10px;border:1px solid rgba(129,140,248,0.1);background:rgba(28,31,53,0.5);text-align:center;font-size:0.75rem;color:#5b6184;cursor:default;">
            <div style="font-size:0.65rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:{{ $color }};margin-bottom:0.15rem;">{{ $role }}</div>
            <div>Role</div>
        </div>
        @endforeach
    </div>

    <script>
        // Password toggle specific to login page
        const toggleBtn = document.getElementById('pwd-toggle-login');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const inp = document.getElementById('password');
                const isText = inp.type === 'text';
                inp.type = isText ? 'password' : 'text';
                this.innerHTML = isText
                    ? `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`
                    : `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
            });
        }

        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const txt = document.getElementById('loginBtnText');
            btn.disabled = true;
            txt.innerHTML = `<svg style="animation:spin 1s linear infinite" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Signing In...`;
        });
    </script>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</x-guest-layout>
