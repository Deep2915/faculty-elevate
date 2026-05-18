<x-guest-layout>
    <!-- Header -->
    <div class="auth-card-header">
        <div class="auth-card-icon">
            <svg width="28" height="28" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
        </div>
        <h2 class="auth-title">Create Account</h2>
        <p class="auth-subtitle">
            Already registered? <a href="{{ route('login') }}">Sign in instead</a>
        </p>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="auth-flash error">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
        @csrf

        <!-- Full Name -->
        <div class="field-group">
            <label class="field-label" for="name">Full Name</label>
            <div class="field-wrap">
                <span class="field-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <input id="name" name="name" type="text" class="field-input"
                    placeholder="Dr. John Smith"
                    value="{{ old('name') }}" required autofocus autocomplete="name">
            </div>
            @error('name')
                <div class="field-error">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email -->
        <div class="field-group">
            <label class="field-label" for="email">Email Address</label>
            <div class="field-wrap">
                <span class="field-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <input id="email" name="email" type="email" class="field-input"
                    placeholder="you@institution.edu"
                    value="{{ old('email') }}" required autocomplete="username">
            </div>
            @error('email')
                <div class="field-error">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Role Selection -->
        <div class="field-group">
            <label class="field-label">Select Your Role</label>
            <div class="role-selector">
                <div>
                    <input type="radio" name="role" id="role_faculty" value="faculty" class="role-option" {{ old('role','faculty')==='faculty'?'checked':'' }}>
                    <label for="role_faculty" class="role-label">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        Faculty
                    </label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_hod" value="hod" class="role-option" {{ old('role')==='hod'?'checked':'' }}>
                    <label for="role_hod" class="role-label">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        HOD
                    </label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_admin" value="admin" class="role-option" {{ old('role')==='admin'?'checked':'' }}>
                    <label for="role_admin" class="role-label">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                        Admin
                    </label>
                </div>
            </div>
            @error('role')
                <div class="field-error"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <div class="field-wrap">
                <span class="field-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <input id="password" name="password" type="password" class="field-input"
                    placeholder="Min 8 characters" required autocomplete="new-password"
                    style="padding-right:2.75rem;">
                <button type="button" class="pwd-toggle" id="pwd-toggle-reg" title="Toggle">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <!-- Strength Meter -->
            <div class="pwd-strength" id="pwd-bars">
                <div class="pwd-bars">
                    <div class="pwd-bar" id="bar1"></div>
                    <div class="pwd-bar" id="bar2"></div>
                    <div class="pwd-bar" id="bar3"></div>
                    <div class="pwd-bar" id="bar4"></div>
                </div>
                <div class="pwd-strength-label" id="pwd-strength-label"></div>
            </div>
            @error('password')
                <div class="field-error"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="field-group">
            <label class="field-label" for="password_confirmation">Confirm Password</label>
            <div class="field-wrap">
                <span class="field-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </span>
                <input id="password_confirmation" name="password_confirmation" type="password" class="field-input"
                    placeholder="Re-enter password" required autocomplete="new-password"
                    style="padding-right:2.75rem;">
                <button type="button" class="pwd-toggle" id="pwd-toggle-confirm" title="Toggle">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <!-- Match indicator -->
            <div id="pwd-match" style="font-size:0.7rem;margin-top:0.375rem;display:none;align-items:center;gap:0.25rem;"></div>
            @error('password_confirmation')
                <div class="field-error"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>
            @enderror
        </div>

        <!-- Terms -->
        <div style="margin-bottom:1.5rem;">
            <label class="check-label" for="terms">
                <input id="terms" type="checkbox" required>
                <span>I agree to the <a href="#" style="color:#818cf8;">Terms of Service</a> and <a href="#" style="color:#818cf8;">Privacy Policy</a></span>
            </label>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-auth" id="registerBtn">
            <span id="registerBtnText" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Create My Account
            </span>
        </button>
    </form>

    <script>
        // Password toggle
        function setupToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            if (!btn) return;
            btn.addEventListener('click', function() {
                const inp = document.getElementById(inputId);
                const isText = inp.type === 'text';
                inp.type = isText ? 'password' : 'text';
                this.innerHTML = isText
                    ? `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`
                    : `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
            });
        }
        setupToggle('pwd-toggle-reg', 'password');
        setupToggle('pwd-toggle-confirm', 'password_confirmation');

        // Strength meter
        const pwdInput = document.getElementById('password');
        pwdInput.addEventListener('input', function() {
            const val = this.value;
            const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'), document.getElementById('bar4')];
            const label = document.getElementById('pwd-strength-label');
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            const levels = ['', 'weak', 'fair', 'good', 'strong'];
            const labels = ['', 'Weak — add uppercase', 'Fair — add numbers', 'Good — add symbols', 'Strong!'];
            const colors = ['', '#f43f5e', '#f59e0b', '#10b981', '#6366f1'];
            bars.forEach((b, i) => {
                b.style.background = i < score ? colors[score] : 'rgba(129,140,248,0.1)';
            });
            label.textContent = val.length ? labels[score] : '';
            label.style.color = val.length ? colors[score] : '';
        });

        // Password match check
        const confirm = document.getElementById('password_confirmation');
        const matchDiv = document.getElementById('pwd-match');
        confirm.addEventListener('input', function() {
            if (!this.value) { matchDiv.style.display = 'none'; return; }
            matchDiv.style.display = 'flex';
            if (this.value === pwdInput.value) {
                matchDiv.innerHTML = `<svg width="12" height="12" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> <span style="color:#10b981;">Passwords match</span>`;
            } else {
                matchDiv.innerHTML = `<svg width="12" height="12" fill="none" stroke="#f43f5e" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> <span style="color:#f43f5e;">Passwords do not match</span>`;
            }
        });

        // Loading state
        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.getElementById('registerBtn');
            const txt = document.getElementById('registerBtnText');
            btn.disabled = true;
            txt.innerHTML = `<svg style="animation:spin 1s linear infinite" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Creating Account...`;
        });
    </script>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</x-guest-layout>
