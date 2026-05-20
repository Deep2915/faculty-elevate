<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Faculty Elevate') }} – Faculty Elevate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

{{-- ══════════════════════════════════
     SIDEBAR
══════════════════════════════════ --}}
<aside class="sidebar" id="sidebar">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
        </div>
        <div>
            <div class="sidebar-logo-text">Faculty Elevate</div>
            <div class="sidebar-logo-sub">{{ ucfirst(auth()->user()->role ?? 'Portal') }} Portal</div>
        </div>
    </div>

    {{-- Nav --}}
    <nav style="flex:1; overflow-y:auto; padding: 0.75rem 0;">
        @php $role = auth()->user()->role ?? 'faculty'; @endphp

        {{-- ── ADMIN ────────────────── --}}
        @if($role === 'admin')
            <div class="sidebar-section-label">Management</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>
            <a href="{{ route('admin.workshops.index') }}" class="nav-item {{ request()->routeIs('admin.workshops*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Workshops
            </a>
            <a href="{{ route('admin.badges.index') }}" class="nav-item {{ request()->routeIs('admin.badges*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                Badges
            </a>
            <div class="sidebar-section-label">Configuration</div>
            <a href="{{ route('admin.weights.edit') }}" class="nav-item {{ request()->routeIs('admin.weights*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                Score Weights
            </a>

        {{-- ── HOD ───────────────────── --}}
        @elseif($role === 'hod')
            <div class="sidebar-section-label">Overview</div>
            <a href="{{ route('hod.dashboard') }}" class="nav-item {{ request()->routeIs('hod.dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <div class="sidebar-section-label">Faculty</div>
            <a href="{{ route('hod.evaluations.index') }}" class="nav-item {{ request()->routeIs('hod.evaluations*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Evaluations
            </a>
            <a href="{{ route('hod.attendance.index') }}" class="nav-item {{ request()->routeIs('hod.attendance*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9" y1="15" x2="12" y2="15"/><line x1="12" y1="15" x2="12" y2="18"/></svg>
                Attendance
            </a>
            <a href="{{ route('hod.feedback.index') }}" class="nav-item {{ request()->routeIs('hod.feedback*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Student Feedback
            </a>
            <a href="{{ route('hod.leaderboard') }}" class="nav-item {{ request()->routeIs('hod.leaderboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                Leaderboard
            </a>

        {{-- ── FACULTY ────────────────── --}}
        @else
            <div class="sidebar-section-label">My Space</div>
            <a href="{{ route('faculty.dashboard') }}" class="nav-item {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="{{ route('faculty.profile') }}" class="nav-item {{ request()->routeIs('faculty.profile') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                My Profile
            </a>
            <a href="{{ route('faculty.roadmap') }}" class="nav-item {{ request()->routeIs('faculty.roadmap') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Growth Roadmap
            </a>
            <div class="sidebar-section-label">Activities</div>
            <a href="{{ route('faculty.achievements.index') }}" class="nav-item {{ request()->routeIs('faculty.achievements*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                Achievements
            </a>
            <a href="{{ route('faculty.goals.index') }}" class="nav-item {{ request()->routeIs('faculty.goals*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                Goals
            </a>
            <a href="{{ route('faculty.workshops.index') }}" class="nav-item {{ request()->routeIs('faculty.workshops*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Workshops
            </a>
            <div class="sidebar-section-label">Insights</div>
            <a href="{{ route('faculty.timetable') }}" class="nav-item {{ request()->routeIs('faculty.timetable') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Timetable
            </a>
            <a href="{{ route('faculty.feedback') }}" class="nav-item {{ request()->routeIs('faculty.feedback') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                My Feedback
            </a>
            <a href="{{ route('faculty.leaderboard') }}" class="nav-item {{ request()->routeIs('faculty.leaderboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                Leaderboard
            </a>
            <a href="{{ route('faculty.wellbeing') }}" class="nav-item {{ request()->routeIs('faculty.wellbeing') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Wellbeing
            </a>
        @endif
    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <div style="display:flex; align-items:center; gap:0.75rem;">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div style="font-size:0.7rem; color:var(--text-muted);">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" style="background:none; border:none; cursor:pointer; color:var(--text-muted); display:flex; align-items:center;" onmouseover="this.style.color='var(--accent-rose)'" onmouseout="this.style.color='var(--text-muted)'">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ══════════════════════════════════
     TOPBAR
══════════════════════════════════ --}}
<header class="topbar">
    <div>
        <h1 class="topbar-title">{{ $header ?? 'Dashboard' }}</h1>
        @isset($breadcrumb)
            <div style="font-size:0.75rem; color:var(--text-muted);">{{ $breadcrumb }}</div>
        @endisset
    </div>
    <div class="topbar-actions">
        {{-- Notifications --}}
        <div class="notif-btn" title="Notifications">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        {{-- Role Badge --}}
        <span class="pill pill-indigo" style="font-size:0.7rem;">{{ strtoupper(auth()->user()->role ?? '') }}</span>
    </div>
</header>

{{-- ══════════════════════════════════
     MAIN CONTENT
══════════════════════════════════ --}}
<main class="main-content">
    <div class="page-body animate-fade-in">
        {{-- Flash Messages --}}
        @if(session('status'))
            <div class="flash-success" style="margin-bottom:1.25rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-2px;margin-right:6px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash-error" style="margin-bottom:1.25rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-2px;margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="flash-error" style="margin-bottom:1.25rem;">
                <ul style="margin:0; padding-left:1.25rem; list-style:disc;">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </div>
</main>

@stack('scripts')
</body>
</html>
