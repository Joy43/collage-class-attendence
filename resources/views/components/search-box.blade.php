{{-- Client-side filter box: <x-search-box id="student-filter" placeholder="Search…" data-filter="#rows .row" /> --}}
@props(['id', 'placeholder' => 'Search…'])

<div {{ $attributes->class('flex max-w-[280px] grow items-center gap-2.5 rounded-[12px] border border-line bg-white/[0.04] px-3.5 py-2 backdrop-blur-md transition-all duration-200 focus-within:border-secondary/60 focus-within:bg-white/[0.08] focus-within:ring-[3px] focus-within:ring-secondary/20') }}>
    <x-icon name="search" class="size-3.5 shrink-0 text-secondary" />
    <input type="text" id="{{ $id }}" placeholder="{{ $placeholder }}" autocomplete="off"
           class="w-full border-0 bg-transparent text-[.825rem] text-fg outline-hidden placeholder:text-muted">
</div>
