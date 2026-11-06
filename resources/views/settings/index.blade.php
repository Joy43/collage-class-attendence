@php
    $tabs = ['faculty' => '👤 Faculty Users', 'sessions' => '📅 Academic Sessions', 'courses' => '📚 Courses'];
@endphp

<x-layouts.app title="Settings">
    <x-page-header title="Settings Configuration" :crumbs="['System', 'Settings']" />

    <div class="mb-5 flex gap-1 overflow-x-auto border-b border-line">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('settings.index', ['tab' => $key]) }}"
               class="-mb-px rounded-t-[10px] border border-b-0 px-4 py-2 text-sm font-semibold whitespace-nowrap transition-colors {{ $tab === $key ? 'border-line bg-surface text-fg' : 'border-transparent text-muted hover:bg-subtle hover:text-fg' }}">{{ $label }}</a>
        @endforeach
    </div>

    @include("settings.partials.{$tab}")
</x-layouts.app>
