{{-- Client-side filter box: <x-search-box id="student-filter" placeholder="Search…" data-filter="#rows .row" /> --}}
@props(['id', 'placeholder' => 'Search…'])

<div {{ $attributes->class('flex max-w-[280px] grow items-center gap-2 rounded-[10px] border border-line bg-subtle px-3 py-1.5') }}>
    <x-icon name="search" class="size-3.5 text-faint" />
    <input type="text" id="{{ $id }}" placeholder="{{ $placeholder }}" autocomplete="off"
           class="w-full border-0 bg-transparent text-[.8rem] text-fg outline-hidden placeholder:text-faint">
</div>
