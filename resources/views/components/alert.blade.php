{{-- <x-alert type="success|error|warning">Message</x-alert> --}}
@props(['type' => 'success'])

@php
    $tone = match ($type) {
        'success' => 'bg-present-soft text-present border-present/25',
        'error' => 'bg-absent-soft text-absent border-absent/25',
        default => 'bg-late-soft text-late border-late/25',
    };
    $emoji = match ($type) { 'success' => '✅', 'error' => '❌', default => '⚠️' };
@endphp

<div {{ $attributes->class("mb-5 flex items-center gap-2.5 rounded-[10px] border px-4 py-3 text-sm font-medium $tone") }} role="alert">
    {{ $emoji }} <span>{{ $slot }}</span>
</div>
