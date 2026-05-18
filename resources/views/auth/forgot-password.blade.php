<x-guest-layout>
    <div class="auth-card-header">
        <div class="auth-card-icon">
            <svg width="28" height="28" fill="none" stroke="#818cf8" stroke-width="2" viewBox="0 0 24 24">
                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
        </div>
        <h2 class="auth-title">Forgot Password?</h2>
        <p class="auth-subtitle">Enter your email and we'll send a reset link</p>
    </div>

    @if (session('status'))
        <div class="auth-flash success">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form" id="forgotForm">
        @csrf

        <div class="field-group">
            <label class="field-label" for="email">Email Address</label>
            <div class="field-wrap">
                <span class="field-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <input id="email" name="email" type="email" class="field-input"
                    placeholder="you@institution.edu"
                    value="{{ old('email') }}" required autofocus>
            </div>
            @error('email')
                <div class="field-error"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth" style="margin-bottom:1.25rem;" id="forgotBtn">
            <span id="forgotBtnText" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Send Reset Link
            </span>
        </button>

        <div style="text-align:center;">
            <a href="{{ route('login') }}" style="font-size:0.8125rem;color:#5b6184;display:inline-flex;align-items:center;gap:0.375rem;transition:color 0.2s;"
               onmouseover="this.style.color='#818cf8'" onmouseout="this.style.color='#5b6184'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to sign in
            </a>
        </div>
    </form>
    <script>
        document.getElementById('forgotForm').addEventListener('submit', function() {
            const btn = document.getElementById('forgotBtn');
            const txt = document.getElementById('forgotBtnText');
            btn.disabled = true;
            txt.innerHTML = `<svg style="animation:spin 1s linear infinite" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Sending...`;
        });
    </script>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</x-guest-layout>
