<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} &mdash; @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

{{-- ============================================================
     TOPBAR  (ungu gelap — sama di semua halaman)
     ============================================================ --}}
<header id="topbar">

    {{-- App Switcher (Dashboard Link) --}}
    <a href="{{ route('dashboard') }}" class="tb-hamburger" aria-label="Go to Dashboard" title="Dashboard">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <rect x="2" y="2" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="7" y="2" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="12" y="2" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="2" y="7" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="7" y="7" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="12" y="7" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="2" y="12" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="7" y="12" width="4" height="4" rx="1" fill="currentColor"/>
            <rect x="12" y="12" width="4" height="4" rx="1" fill="currentColor"/>
        </svg>
    </a>

    {{-- Module nav links — diisi tiap modul --}}
    <nav class="tb-nav" aria-label="Navigasi modul">
        @yield('topbar-nav')
    </nav>

    {{-- Kanan: nama org + avatar --}}
    <div class="tb-right">
        <span class="tb-orgname">{{ config('app.name') }}</span>

        {{-- User avatar dropdown (Alpine.js) --}}
        <div class="tb-user" x-data="{ open: false }" @click.outside="open = false">
            <button class="tb-avatar" @click="open = !open" :aria-expanded="open">
                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
            </button>
            <div class="tb-dropdown" x-show="open" x-transition x-cloak>
                <div class="tbd-header">
                    <strong>{{ auth()->user()?->name }}</strong>
                    <span>{{ auth()->user()?->email }}</span>
                </div>
                <a href="{{ route('profile.edit') }}" class="tbd-item">Profil Saya</a>
                <hr class="tbd-divider">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="tbd-item tbd-item--danger">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- ============================================================
     SUBBAR  (putih — tombol aksi + judul + search + pagination)
     ============================================================ --}}
<div id="subbar">
    <div class="sb-left">
        @yield('subbar-actions')
        <h1 class="sb-title">
            @yield('page-title', 'Halaman')
            @hasSection('page-title-settings')
                <button class="sb-settings-btn" title="Pengaturan">
                    @yield('page-title-settings')
                </button>
            @endif
        </h1>
    </div>

    <div class="sb-right">
        @hasSection('subbar-search')
            <div class="sb-search-wrap">
                <svg class="sb-search-icon" width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M10 10l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                @yield('subbar-search')
                <button class="sb-search-caret">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <path d="M2 3.5l3 3 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        @endif

        @hasSection('subbar-pagination')
            <div class="sb-pagination">
                @yield('subbar-pagination')
            </div>
        @endif

        @hasSection('subbar-viewmode')
            <div class="sb-viewmode">
                @yield('subbar-viewmode')
            </div>
        @endif

        @yield('subbar-extra')
    </div>
</div>

{{-- ============================================================
     LAYOUT BODY
     Sidebar (opsional) + konten utama
     ============================================================ --}}
<div id="layout-body">

    @hasSection('sidebar')
        <aside id="sidebar" class="{{ session('sidebar_collapsed') ? 'collapsed' : '' }}">
            <button class="sd-minimizer" onclick="toggleSidebar()" aria-label="Toggle Sidebar" title="Toggle Sidebar">
                <svg class="icon-minimize" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
                <svg class="icon-maximize" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="15" y1="3" x2="15" y2="21"></line>
                </svg>
            </button>
            <div class="sidebar-inner">
                @yield('sidebar')
            </div>
        </aside>
    @endif

    <main id="main-content">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6.5" stroke="currentColor" stroke-width="1.3"/><path d="M4.5 7.5l2 2.5 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6.5" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v4M7.5 10.5h.01" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
        
        sidebar.classList.toggle('collapsed');
        
        const isCollapsed = sidebar.classList.contains('collapsed');
        const minIcon = sidebar.querySelector('.icon-minimize');
        const maxIcon = sidebar.querySelector('.icon-maximize');
        
        if (minIcon && maxIcon) {
            minIcon.style.display = isCollapsed ? 'none' : 'block';
            maxIcon.style.display = isCollapsed ? 'block' : 'none';
        }
    }

    // Set initial icon state if it loads collapsed
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList.contains('collapsed')) {
            const minIcon = sidebar.querySelector('.icon-minimize');
            const maxIcon = sidebar.querySelector('.icon-maximize');
            if (minIcon && maxIcon) {
                minIcon.style.display = 'none';
                maxIcon.style.display = 'block';
            }
        }
    });
</script>
</body>
</html>
