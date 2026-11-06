@php
    $mainNav = [
        ['dashboard', 'dashboard', 'grid', 'Dashboard'],
        ['attendance.create', 'attendance.*', 'clipboard', 'Attendance'],
        ['students.index', 'students.*', 'users', 'Students'],
        ['reports.index', 'reports.*', 'bar-chart', 'Reports'],
    ];

    $linkClasses = fn (bool $active) => 'group mb-0.5 flex items-center gap-2.5 rounded-[10px] border px-3 py-[9px] text-sm font-medium whitespace-nowrap transition-all duration-150 '
        . ($active
            ? 'border-primary/25 bg-primary/15 text-white'
            : 'border-transparent text-slate-400 hover:bg-slate-800 hover:text-slate-200');
    $iconClasses = fn (bool $active) => $active ? 'text-blue-400' : 'opacity-75 group-hover:opacity-100';
@endphp

<aside id="sidebar" class="scroll-none fixed inset-y-0 left-0 z-[200] flex h-full w-[260px] shrink-0 -translate-x-full flex-col overflow-x-hidden overflow-y-auto bg-slate-900 shadow-xl transition-transform duration-200 lg:static lg:translate-x-0 lg:shadow-none">
    <div class="flex items-center gap-2.5 border-b border-white/5 px-[18px] py-5">
        <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary text-[.9rem] font-extrabold text-white shadow-[0_4px_12px_rgba(37,99,235,.4)]">A</div>
        <div>
            <div class="text-base font-bold text-slate-50">{{ config('app.name') }}</div>
            <div class="mt-px text-[.65rem] font-medium text-slate-500">Management System</div>
        </div>
    </div>

    <div class="px-[18px] pt-[18px] pb-1.5 text-[.67rem] font-bold tracking-wider text-slate-600 uppercase">Main Menu</div>
    <nav class="px-2.5 py-2">
        @foreach ($mainNav as [$route, $pattern, $icon, $label])
            @php $active = request()->routeIs($pattern); @endphp
            <a href="{{ route($route) }}" class="{{ $linkClasses($active) }}">
                <x-icon :name="$icon" class="size-[18px] {{ $iconClasses($active) }}" /> {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="px-[18px] pt-[18px] pb-1.5 text-[.67rem] font-bold tracking-wider text-slate-600 uppercase">System</div>
    <nav class="px-2.5 py-2">
        @php $active = request()->routeIs('notifications.*'); @endphp
        <a href="{{ route('notifications.index') }}" class="{{ $linkClasses($active) }}">
            <x-icon name="bell" class="size-[18px] {{ $iconClasses($active) }}" />
            <span>Notifications</span>
            <span id="sidebar-notif-badge" class="ml-auto hidden min-w-5 justify-center rounded-full bg-primary px-[7px] py-px text-[.68rem] font-bold text-white">0</span>
        </a>
        @php $active = request()->routeIs('settings.*'); @endphp
        <a href="{{ route('settings.index') }}" class="{{ $linkClasses($active) }}">
            <x-icon name="settings" class="size-[18px] {{ $iconClasses($active) }}" /> Settings
        </a>
    </nav>

    <div class="mt-auto border-t border-white/5 px-2.5 py-3.5">
        <div class="flex items-center gap-2.5 rounded-[10px] px-3 py-2.5 transition-colors hover:bg-slate-800">
            <x-avatar :text="auth()->user()->initials()" />
            <div>
                <div class="text-[.825rem] font-semibold text-slate-200">{{ auth()->user()->user_name }}</div>
                <div class="text-[.7rem] text-slate-500">Faculty Member</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="mt-1 flex w-full items-center gap-2 rounded-lg px-3 py-2 text-[.8rem] font-semibold text-slate-400 transition-all duration-150 hover:bg-red-600/10 hover:text-red-300">
                <x-icon name="log-out" class="size-3.5" /> Sign Out
            </button>
        </form>
    </div>
</aside>
