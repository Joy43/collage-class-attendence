{{-- <x-status-chip :status="$attendance->status" /> --}}
@props(['status'])

<span {{ $attributes->class(['inline-flex items-center gap-[5px] whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold transition-all duration-150', $status->chipClass()]) }}>{{ $slot->isEmpty() ? $status->value : $slot }}</span>
