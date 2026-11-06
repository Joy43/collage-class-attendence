@php
    $iconBtn = 'relative flex size-9 sm:size-10 items-center justify-center rounded-xl border border-line bg-surface/80 text-muted transition-all duration-150 hover:border-secondary/40 hover:bg-white/[0.12] hover:text-fg hover:shadow-[0_0_12px_rgba(56,189,248,0.2)] active:scale-95 [&_svg]:size-[18px] sm:[&_svg]:size-[19px]';
@endphp

<header class="flex h-16 shrink-0 items-center gap-3 border-b border-line bg-surface/90 px-4 backdrop-blur-2xl sm:px-6">
    <button type="button" id="menu-btn" class="{{ $iconBtn }} lg:hidden" aria-label="Open navigation menu">
        <x-icon name="menu" />
    </button>

    <div class="flex max-w-[380px] grow items-center gap-2.5 rounded-full border border-line bg-white/[0.04] px-4 py-2 backdrop-blur-md transition-all duration-200 focus-within:border-secondary/60 focus-within:bg-white/[0.08] focus-within:ring-[3px] focus-within:ring-secondary/20">
        <x-icon name="search" class="size-4 shrink-0 text-secondary" />
        <input type="text" placeholder="Search students, courses…" class="w-full border-0 bg-transparent text-sm text-fg outline-hidden placeholder:text-muted">
    </div>

    <div class="ml-auto flex items-center gap-2">
        {{-- Notification bell + dropdown (populated by resources/js/notifications.js) --}}
        <div class="relative" id="notif-wrap">
            <button type="button" id="notif-btn" title="Notifications" class="{{ $iconBtn }}">
                <x-icon name="bell" />
                <span id="notif-dot" class="absolute top-2 right-2 hidden size-2 rounded-full border border-surface bg-secondary shadow-[0_0_8px_#38BDF8]"></span>
            </button>
            <span id="notif-badge" class="pointer-events-none absolute -top-1 -right-1 hidden h-[18px] min-w-[18px] items-center justify-center rounded-full border-2 border-[#0B1020] bg-linear-to-r from-primary to-secondary px-1 text-[.65rem] leading-none font-black text-white shadow-[0_0_10px_rgba(56,189,248,0.5)]">0</span>

            <div id="notif-panel" class="absolute top-[calc(100%+12px)] right-0 z-[9999] hidden w-[min(380px,calc(100vw-2rem))] overflow-hidden rounded-[18px] border border-line bg-[#0B1020]/95 shadow-[0_20px_50px_rgba(0,0,0,0.6)] backdrop-blur-2xl">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base font-bold text-fg">Notifications</span>
                        <span class="size-2 rounded-full bg-secondary shadow-[0_0_6px_#38BDF8]"></span>
                    </div>
                    <button type="button" id="notif-mark-all" class="text-xs font-bold text-secondary transition-colors hover:underline">Mark all read</button>
                </div>
                <div id="notif-list" class="scroll-thin max-h-[380px] overflow-y-auto">
                    <div class="p-8 text-center text-sm text-muted">No notifications yet</div>
                </div>
                <div class="flex items-center justify-between border-t border-line bg-subtle/50 px-5 py-3 text-xs">
                    <button type="button" id="notif-clear-read" class="font-medium text-muted transition-colors hover:text-fg">Clear read</button>
                    <a href="{{ route('notifications.index') }}" class="font-bold text-secondary transition-colors hover:underline">View all →</a>
                </div>
            </div>
        </div>

        <button type="button" id="theme-btn" title="Toggle theme" class="{{ $iconBtn }}">
            <x-icon name="moon" class="dark:hidden" />
            <x-icon name="sun" class="hidden text-secondary dark:block" />
        </button>

        <div class="mx-1 h-6 w-px bg-line"></div>

        <x-avatar :text="auth()->user()->initials()" size="size-9 sm:size-10 text-[.825rem]" title="{{ auth()->user()->user_name }}" class="border-2 border-line/80 shadow-md transition-all duration-200 hover:scale-105 hover:border-secondary hover:shadow-[0_0_12px_rgba(56,189,248,0.4)]" />
    </div>
</header>
