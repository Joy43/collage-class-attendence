{{--
    <x-card title="Session Details">
        <x-slot:actions> …header right side… </x-slot:actions>
        <x-slot:toolbar> …search/filters row… </x-slot:toolbar>
        body
        <x-slot:footer> … </x-slot:footer>
    </x-card>
    Pass :flush="true" to drop body padding (tables, lists).
--}}
@props(['title' => null, 'flush' => false])

<div {{ $attributes->class('overflow-hidden rounded-[14px] border border-line bg-surface shadow-sm') }}>
    @if ($title || isset($actions))
        <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-line px-5 py-4">
            <span class="text-[.9rem] font-bold">{{ $title }}</span>
            {{ $actions ?? '' }}
        </div>
    @endif

    @isset($toolbar)
        <div class="flex flex-wrap items-center gap-2.5 border-b border-line px-4 py-3">{{ $toolbar }}</div>
    @endisset

    <div @class(['p-5' => ! $flush])>{{ $slot }}</div>

    @isset($footer)
        <div class="border-t border-line px-5 py-3 text-[.8rem] text-muted">{{ $footer }}</div>
    @endisset
</div>
