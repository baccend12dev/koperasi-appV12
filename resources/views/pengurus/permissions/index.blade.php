@extends('layouts.app')

@section('title', 'Manajemen Permission')

@section('topbar-nav')
    <a href="{{ route('pengurus.users.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.users.index') ? 'active' : '' }}">
        User
    </a>
    <a href="{{ route('pengurus.roles.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.roles.index') ? 'active' : '' }}">
        Role
    </a>
    <a href="{{ route('pengurus.permissions.index') }}"
       class="tb-link {{ request()->routeIs('pengurus.permissions.index') ? 'active' : '' }}">
        Permission
    </a>
@endsection

@section('page-title', 'Manajemen Permission')

@section('sidebar')
    <div class="sd-section">
        <div class="sd-heading">
            <div style="display:flex;align-items:center;gap:5px">
                <svg class="sd-heading-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                PENGURUS
            </div>
        </div>

        <a href="{{ route('pengurus.users.index') }}"
           class="sd-link {{ request()->routeIs('pengurus.users.*') ? 'active' : '' }}">
            User
        </a>
        <a href="{{ route('pengurus.roles.index') }}"
           class="sd-link {{ request()->routeIs('pengurus.roles.*') ? 'active' : '' }}">
            Role
        </a>
        <a href="{{ route('pengurus.permissions.index') }}"
           class="sd-link {{ request()->routeIs('pengurus.permissions.*') ? 'active' : '' }}">
            Permission
        </a>
    </div>
@endsection

@section('content')
    <div style="padding: 60px 24px; text-align: center; background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border); margin: 24px;">
        <div style="width: 80px; height: 80px; background-color: var(--accent-light); color: var(--accent-text); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h2 style="font-size: 20px; font-weight: 700; color: var(--text-1); margin-bottom: 8px;">Modul Permission</h2>
        <p style="font-size: 14px; color: var(--text-2); max-width: 400px; margin: 0 auto;">
            Modul Permission akan diimplementasikan pada tahap berikutnya.
        </p>
    </div>
@endsection
