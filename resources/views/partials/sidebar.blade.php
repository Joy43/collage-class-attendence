@php
    $mainNav = [
        ['dashboard', 'dashboard', 'grid', 'Dashboard'],
        ['attendance.create', 'attendance.*', 'clipboard', 'Attendance'],
        ['students.index', 'students.*', 'users', 'Students'],
        ['reports.index', 'reports.*', 'bar-chart', 'Reports'],
    ];

    $linkClasses = fn (bool $active) => 'group mb-1 flex items-center gap-3 rounded-[12px] border px-3.5 py-2.5 text-sm font-semibold whitespace-nowrap transition-all duration-200 '
        . ($active
            ? 'border-secondary/40 bg-linear-to-r from-primary/30 to-secondary/15 text-white shadow-[0_0_18px_rgba(56,189,248,0.18)]'
            : 'border-transparent text-muted hover:border-line hover:bg-white/[0.06] hover:text-white');
    $iconClasses = fn (bool $active) => $active
        ? 'text-secondary drop-shadow-[0_0_8px_rgba(56,189,248,0.6)]'
        : 'text-muted group-hover:text-secondary group-hover:scale-110';
@endphp

<!-- Mobile Backdrop Overlay -->
<div id="sidebar-backdrop" class="fixed inset-0 z-[190] hidden bg-black/60 backdrop-blur-sm transition-opacity duration-200 lg:hidden"></div>

<aside id="sidebar" class="scroll-none fixed inset-y-0 left-0 z-[200] flex h-full w-[265px] shrink-0 -translate-x-full flex-col overflow-x-hidden overflow-y-auto border-r border-line bg-[#0B1020]/92 shadow-2xl backdrop-blur-2xl transition-transform duration-250 lg:static lg:translate-x-0 lg:shadow-none">
    <div class="flex items-center gap-3 border-b border-line px-5 py-5">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-tr from-primary to-secondary text-base font-black text-white shadow-[0_0_20px_rgba(37,99,235,0.45)]">
            A
        </div>
        <div>
            <div class="text-base font-extrabold tracking-tight text-fg">{{ config('app.name') }}</div>
            <div class="mt-0.5 flex items-center gap-1.5 text-[.68rem] font-medium text-secondary">
                <span class="size-1.5 rounded-full bg-secondary shadow-[0_0_6px_#38BDF8]"></span>
                <span>Management System</span>
            </div>
        </div>
        <button type="button" id="sidebar-close-btn" class="ml-auto flex size-8 items-center justify-center rounded-lg border border-line bg-surface text-muted hover:text-fg lg:hidden" aria-label="Close sidebar">
            <x-icon name="x" class="size-4" />
        </button>
    </div>

    <div class="px-5 pt-5 pb-1.5 text-[.68rem] font-extrabold tracking-wider text-muted uppercase">Main Menu</div>
    <nav class="px-3 py-1">
        @foreach ($mainNav as [$route, $pattern, $icon, $label])
            @php $active = request()->routeIs($pattern); @endphp
            <a href="{{ route($route) }}" class="{{ $linkClasses($active) }}">
                <x-icon :name="$icon" class="size-[19px] {{ $iconClasses($active) }}" />
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-5 pt-5 pb-1.5 text-[.68rem] font-extrabold tracking-wider text-muted uppercase">System</div>
    <nav class="px-3 py-1">
        @php $active = request()->routeIs('notifications.*'); @endphp
        <a href="{{ route('notifications.index') }}" class="{{ $linkClasses($active) }}">
            <x-icon name="bell" class="size-[19px] {{ $iconClasses($active) }}" />
            <span>Notifications</span>
            <span id="sidebar-notif-badge" class="ml-auto hidden min-w-5 justify-center rounded-full bg-linear-to-r from-primary to-secondary px-2 py-0.5 text-[.68rem] font-bold text-white shadow-[0_0_10px_rgba(56,189,248,0.5)]">0</span>
        </a>
        @php $active = request()->routeIs('settings.*'); @endphp
        <a href="{{ route('settings.index') }}" class="{{ $linkClasses($active) }}">
            <x-icon name="settings" class="size-[19px] {{ $iconClasses($active) }}" />
            <span>Settings</span>
        </a>
    </nav>

    <div class="mt-auto border-t border-line p-3.5">
        <div class="flex items-center gap-3 rounded-xl border border-line bg-surface/60 p-2.5 backdrop-blur-md transition-colors hover:bg-white/[0.08]">
            <x-avatar :text="auth()->user()->initials()" size="size-9 text-[.8rem]" />
            <div class="min-w-0 flex-1">
                <div class="truncate text-[.825rem] font-bold text-fg">{{ auth()->user()->user_name }}</div>
                <div class="truncate text-[.7rem] font-medium text-muted">Faculty Member</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl border border-transparent px-3 py-2 text-[.8rem] font-semibold text-muted transition-all duration-150 hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-300 active:scale-[.98]">
                <x-icon name="log-out" class="size-4" />
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>
