{{-- <x-alert type="success|error|warning">Message</x-alert> --}}
@props(['type' => 'success'])

@php
    $tone = match ($type) {
        'success' => 'bg-present-soft text-present border-present/30 shadow-[0_0_15px_rgba(16,185,129,0.15)]',
        'error' => 'bg-absent-soft text-absent border-absent/30 shadow-[0_0_15px_rgba(239,68,68,0.15)]',
        default => 'bg-late-soft text-late border-late/30 shadow-[0_0_15px_rgba(245,158,11,0.15)]',
    };
    $emoji = match ($type) { 'success' => '✅', 'error' => '❌', default => '⚠️' };
@endphp

<div {{ $attributes->class("mb-5 flex items-center gap-3 rounded-[14px] border px-4 py-3 text-sm font-medium backdrop-blur-md $tone") }} role="alert">
    <span>{{ $emoji }}</span>
    <span>{{ $slot }}</span>
</div>
