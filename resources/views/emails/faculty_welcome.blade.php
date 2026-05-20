<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to Faculty Elevate</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Inter', Arial, sans-serif; background: #0d0f1a; color: #f0f2ff; }
  .wrapper { max-width: 600px; margin: 40px auto; background: #141624; border-radius: 20px; overflow: hidden; border: 1px solid rgba(129,140,248,0.2); }
  .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 48px 40px 40px; text-align: center; }
  .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; }
  .header h1 { font-size: 28px; font-weight: 700; color: #fff; margin-bottom: 8px; }
  .header p { font-size: 15px; color: rgba(255,255,255,0.75); }
  .body { padding: 40px; }
  .greeting { font-size: 18px; font-weight: 600; color: #f0f2ff; margin-bottom: 16px; }
  .text { font-size: 14px; color: #9ca3c8; line-height: 1.7; margin-bottom: 24px; }
  .credentials-box { background: #0d0f1a; border: 1px solid rgba(129,140,248,0.25); border-radius: 14px; padding: 24px; margin-bottom: 28px; }
  .cred-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #6366f1; margin-bottom: 16px; }
  .cred-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
  .cred-row:last-child { border-bottom: none; }
  .cred-label { font-size: 12px; color: #5b6184; width: 90px; flex-shrink: 0; }
  .cred-value { font-size: 14px; font-weight: 600; color: #f0f2ff; }
  .cred-password { font-family: monospace; font-size: 16px; color: #818cf8; background: rgba(99,102,241,0.12); padding: 4px 10px; border-radius: 6px; }
  .role-badge { display: inline-block; padding: 3px 10px; background: rgba(99,102,241,0.18); color: #a5b4fc; border-radius: 99px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
  .btn-wrapper { text-align: center; margin-bottom: 28px; }
  .btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #6366f1, #4338ca); color: #fff; text-decoration: none; border-radius: 10px; font-size: 15px; font-weight: 600; }
  .warning-box { background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 16px 20px; margin-bottom: 28px; font-size: 13px; color: #fcd34d; line-height: 1.6; }
  .footer { padding: 24px 40px; background: #0d0f1a; border-top: 1px solid rgba(255,255,255,0.05); text-align: center; font-size: 12px; color: #5b6184; }
  .footer strong { color: #818cf8; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="header-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
      </svg>
    </div>
    <h1>Welcome to Faculty Elevate</h1>
    <p>Your account has been created by the administrator</p>
  </div>

  <div class="body">
    <div class="greeting">Hello, {{ $recipientName }}!</div>
    <p class="text">
      Your Faculty Elevate account is ready. This platform helps track your academic performance,
      manage goals, collect student feedback, and celebrate your achievements. Below are your
      login credentials — please change your password after your first sign-in.
    </p>

    <div class="credentials-box">
      <div class="cred-title">Your Login Credentials</div>
      <div class="cred-row">
        <span class="cred-label">Email</span>
        <span class="cred-value">{{ $recipientEmail }}</span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Password</span>
        <span class="cred-value"><span class="cred-password">{{ $plainPassword }}</span></span>
      </div>
      <div class="cred-row">
        <span class="cred-label">Role</span>
        <span class="cred-value"><span class="role-badge">{{ $role }}</span></span>
      </div>
    </div>

    <div class="btn-wrapper">
      <a href="{{ url('/login') }}" class="btn">Sign In Now →</a>
    </div>

    <div class="warning-box">
      <strong>Security Notice:</strong> This email contains your temporary password. Please log in and change it immediately via your profile settings. Do not share these credentials with anyone.
    </div>

    <p class="text">
      If you have any questions or need assistance, please contact your department administrator.
      We look forward to supporting your professional growth journey!
    </p>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} <strong>Faculty Elevate</strong> &mdash; Academic Performance Platform</p>
    <p style="margin-top:8px;">This is an automated message. Please do not reply to this email.</p>
  </div>
</div>
</body>
</html>
