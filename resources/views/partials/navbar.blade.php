{{-- resources/views/partials/navbar.blade.php --}}
@auth
    @php
        $user = auth()->user();
        $role = $user->role?->name;
        // تحديد الاسم المعروض بناءً على الدور
        if ($role === 'teacher') {
            $displayName = $user->teacher?->first_name;
        } else {
            $displayName = $user->admin?->first_name ?? $user->email;
        }
    @endphp

        <header class="app-navbar">
            <div class="navbar-left">
            <button class="toggle-sidebar" aria-label="Toggle sidebar">
                <!-- أيقونة همبرغر -->
                <svg width="24" height="24" fill="currentColor"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
            <h1 class="app-title">معهد القرآن</h1>
        </div>

        <div class="navbar-right">
            <div class="user-welcome">
                مرحباً، <strong>{{ $displayName }}</strong>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    تسجيل الخروج   
                    <svg width="20" height="20" fill="currentColor" style="margin-left:6px;"><path d="M16 17l5-5-5-5M21 12H9"/><path d="M13 5v-1a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-1"/></svg>
                </button>
            </form>
        </div>
    </header>
@endauth
