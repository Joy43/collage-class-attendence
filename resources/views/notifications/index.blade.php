@php
    use App\Enums\NotificationType;

    // key => [label, emoji, count text colour, active bg, active border, active ring]
    $tabs = [
        'all' => ['All', '📋', 'text-primary', 'bg-primary-soft', 'border-primary', 'ring-primary/20'],
        'unread' => ['Unread', '🔵', 'text-leave', 'bg-leave-soft', 'border-leave', 'ring-leave/20'],
        'create' => ['Created', '➕', 'text-present', 'bg-present-soft', 'border-present', 'ring-present/20'],
        'update' => ['Updated', '✏️', 'text-primary', 'bg-primary-soft', 'border-primary', 'ring-primary/20'],
        'delete' => ['Deleted', '🗑️', 'text-absent', 'bg-absent-soft', 'border-absent', 'ring-absent/20'],
    ];
    $titles = ['all' => 'All', 'unread' => 'Unread', 'create' => 'Student Created', 'update' => 'Student Updated', 'delete' => 'Student Deleted'];
@endphp

<x-layouts.app title="Notifications">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <x-page-header title="🔔 Notifications" :crumbs="['Home', 'Notifications']" class="mb-0">
            @if ($counts['unread'] > 0)
                <strong class="text-absent">{{ $counts['unread'] }} unread</strong> of {{ $counts['all'] }} total
            @else
                {{ $counts['all'] }} total notifications
            @endif
        </x-page-header>

        <div class="flex flex-wrap items-center gap-2">
            @if ($counts['unread'] > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}" onsubmit="return confirm('Mark all as read?')">
                    @csrf @method('PATCH')
                    <x-button type="submit" variant="secondary" size="sm">Mark All Read</x-button>
                </form>
            @endif
            @if ($counts['all'] > 0)
                <form method="POST" action="{{ route('notifications.destroy-all') }}" onsubmit="return confirm('Delete ALL notifications? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm">Clear All</x-button>
                </form>
            @endif
        </div>
    </div>

    {{-- Filter tiles --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-[repeat(auto-fit,minmax(140px,1fr))]">
        @foreach ($tabs as $key => [$label, $emoji, $text, $soft, $border, $ring])
            <a href="{{ route('notifications.index', ['filter' => $key]) }}"
               class="flex flex-col gap-1.5 rounded-[14px] border-2 p-4 transition-all duration-150 {{ $filter === $key ? "$soft $border ring-[3px] $ring" : 'border-line bg-surface shadow-sm hover:border-line-hover' }}">
                <span class="text-[1.2rem]">{{ $emoji }}</span>
                <span class="text-2xl font-extrabold tracking-tighter {{ $text }}">{{ $counts[$key] }}</span>
                <span class="text-[.72rem] font-bold tracking-wider text-muted uppercase">{{ $label }}</span>
            </a>
        @endforeach
    </div>

    <x-card :flush="true">
        <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-line px-5 py-4">
            <span class="text-[.9rem] font-bold">
                {{ $titles[$filter] }} Notifications
                <span class="ml-1.5 text-[.8rem] font-medium text-muted">({{ $notifications->count() }})</span>
            </span>
            <x-search-box id="notif-search" placeholder="Search notifications…" data-filter=".notif-row" />
        </div>

        @forelse ($notifications as $notification)
            @php
                $type = $notification->type;
                $unread = ! $notification->is_read;
            @endphp
            <div class="notif-row flex flex-col gap-3 border-b border-line px-5 py-4 transition-colors sm:flex-row sm:items-start sm:gap-4 {{ $unread ? $type->softClass() : '' }}"
                 data-search="{{ strtolower($notification->title . ' ' . $notification->message) }}"
                 @if ($unread) data-unread-id="{{ $notification->id }}" data-unread-bg="{{ $type->softClass() }}" @endif>

                <div class="mt-0.5 flex size-11 shrink-0 items-center justify-center rounded-xl border text-[1.2rem] {{ $type->softClass() }} {{ $type->ringClass() }}">{{ $type->icon() }}</div>

                <div class="min-w-0 grow">
                    <div class="mb-1 flex flex-wrap items-center gap-2">
                        <span class="text-sm font-bold {{ $type->textClass() }}">{{ $notification->title }}</span>
                        <span class="rounded-full border px-2 py-0.5 text-[.68rem] font-bold tracking-wider uppercase {{ $type->softClass() }} {{ $type->textClass() }} {{ $type->ringClass() }}">{{ $type->label() }}</span>
                        @if ($unread)<span data-unread-dot title="Unread" class="size-2 shrink-0 rounded-full bg-absent"></span>@endif
                    </div>
                    <div class="mb-1.5 text-[.85rem] leading-normal text-muted">{{ $notification->message }}</div>
                    <div class="flex items-center gap-1.5 text-xs text-faint">
                        <x-icon name="calendar" class="size-3 opacity-60" />
                        <span>{{ $notification->created_at->format('D, M j, Y · g:i A') }}</span>
                        <span class="text-line">·</span>
                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="flex shrink-0 flex-row items-center gap-1.5 sm:flex-col sm:items-end">
                    @if ($unread)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf @method('PATCH')
                            <x-button type="submit" variant="secondary" size="sm">Mark Read</x-button>
                        </form>
                    @else
                        <span class="rounded-md bg-present-soft px-2 py-1 text-[.72rem] font-semibold whitespace-nowrap text-present">✓ Read</span>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                        @csrf @method('DELETE')
                        <x-button type="submit" variant="danger-soft" size="sm">Delete</x-button>
                    </form>
                </div>
            </div>
        @empty
            <x-empty-state icon="🔕" title="No notifications here">
                @if ($filter !== 'all')
                    <a href="{{ route('notifications.index') }}" class="text-primary">View all notifications</a>
                @else
                    Notifications will appear here when students are added, updated, or deleted.
                @endif
            </x-empty-state>
        @endforelse

        @if ($notifications->isNotEmpty())
            <x-slot:footer>
                Showing {{ $notifications->count() }} {{ Str::plural('notification', $notifications->count()) }}
            </x-slot:footer>
        @endif
    </x-card>
</x-layouts.app>
