@php
    $tabs = ['faculty' => '👤 Faculty Users', 'sessions' => '📅 Academic Sessions', 'courses' => '📚 Courses'];
@endphp

<x-layouts.app title="Settings">
    <x-page-header title="Settings Configuration" :crumbs="['System', 'Settings']" />

    <div class="mb-5 flex gap-1.5 overflow-x-auto border-b border-line pb-px">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('settings.index', ['tab' => $key]) }}"
               class="-mb-px rounded-t-[12px] border border-b-0 px-5 py-2.5 text-sm font-bold whitespace-nowrap transition-all duration-150 {{ $tab === $key ? 'border-secondary/40 bg-surface text-secondary shadow-[0_-4px_12px_rgba(56,189,248,0.15)]' : 'border-transparent text-muted hover:bg-white/[0.04] hover:text-fg' }}">{{ $label }}</a>
        @endforeach
    </div>

    @include("settings.partials.{$tab}")
</x-layouts.app>
