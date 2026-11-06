@php
    $iconBtn = 'relative flex size-[38px] items-center justify-center rounded-[10px] border border-transparent text-muted transition-all duration-150 hover:border-line hover:bg-subtle hover:text-fg [&_svg]:size-[18px]';
@endphp

<header class="flex h-16 shrink-0 items-center gap-3 border-b border-line bg-surface px-4 sm:px-6">
    <button type="button" id="menu-btn" class="{{ $iconBtn }} lg:hidden" aria-label="Menu"><x-icon name="menu" /></button>

    <div class="flex max-w-[380px] grow items-center gap-2 rounded-full border border-line bg-subtle px-3.5 py-[7px] transition-all duration-150 focus-within:border-primary focus-within:bg-surface focus-within:ring-[3px] focus-within:ring-primary/20">
        <x-icon name="search" class="size-4 text-faint" />
        <input type="text" placeholder="Search students, courses…" class="w-full border-0 bg-transparent text-sm text-fg outline-hidden placeholder:text-faint">
    </div>

    <div class="ml-auto flex items-center gap-1">
        {{-- Notification bell + dropdown (populated by resources/js/notifications.js) --}}
        <div class="relative" id="notif-wrap">
            <button type="button" id="notif-btn" title="Notifications" class="{{ $iconBtn }}">
                <x-icon name="bell" />
                <span id="notif-dot" class="absolute top-1.5 right-1.5 hidden size-2 rounded-full border-2 border-surface bg-absent"></span>
            </button>
            <span id="notif-badge" class="pointer-events-none absolute -top-1 -right-1 hidden h-[17px] min-w-[17px] items-center justify-center rounded-full border-2 border-surface bg-absent px-1 text-[.62rem] leading-none font-extrabold text-white">0</span>

            <div id="notif-panel" class="absolute top-[calc(100%+10px)] right-0 z-[9999] hidden w-[min(360px,calc(100vw-2rem))] overflow-hidden rounded-[14px] border border-line bg-surface shadow-xl">
                <div class="flex items-center justify-between border-b border-line px-4 py-3.5">
                    <span class="text-[.9rem] font-bold">🔔 Notifications</span>
                    <button type="button" id="notif-mark-all" class="text-xs font-semibold text-primary">Mark all read</button>
                </div>
                <div id="notif-list" class="scroll-thin max-h-[380px] overflow-y-auto">
                    <div class="p-8 text-center text-[.85rem] text-muted">No notifications yet</div>
                </div>
                <div class="flex items-center justify-between border-t border-line px-4 py-2.5 text-[.78rem]">
                    <button type="button" id="notif-clear-read" class="text-muted hover:text-fg">Clear read</button>
                    <a href="{{ route('notifications.index') }}" class="font-semibold text-primary">View all</a>
                </div>
            </div>
        </div>

        <button type="button" id="theme-btn" title="Toggle theme" class="{{ $iconBtn }}">
            <x-icon name="moon" class="dark:hidden" />
            <x-icon name="sun" class="hidden dark:block" />
        </button>

        <div class="mx-1 h-6 w-px bg-line"></div>

        <x-avatar :text="auth()->user()->initials()" size="size-9 text-[.8rem]" title="{{ auth()->user()->user_name }}" class="border-2 border-transparent transition-colors hover:border-primary" />
    </div>
</header>
