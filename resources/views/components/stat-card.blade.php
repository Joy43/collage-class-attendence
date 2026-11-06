{{-- Dashboard KPI tile. tone: primary | present | absent | late | leave | holiday --}}
@props(['tone' => 'primary', 'emoji', 'label', 'value', 'footer' => null, 'progress' => null])

@php
    $tones = [
        'primary' => ['bar' => 'bg-primary', 'icon' => 'bg-primary-soft text-primary'],
        'present' => ['bar' => 'bg-present', 'icon' => 'bg-present-soft text-present'],
        'absent' => ['bar' => 'bg-absent', 'icon' => 'bg-absent-soft text-absent'],
        'late' => ['bar' => 'bg-late', 'icon' => 'bg-late-soft text-late'],
        'leave' => ['bar' => 'bg-leave', 'icon' => 'bg-leave-soft text-leave'],
        'holiday' => ['bar' => 'bg-holiday', 'icon' => 'bg-holiday-soft text-holiday'],
    ][$tone];
@endphp

<div class="relative flex flex-col gap-3 overflow-hidden rounded-[14px] border border-line bg-surface p-5 shadow-sm transition-all duration-150 hover:-translate-y-px hover:shadow-md">
    <div class="absolute inset-x-0 top-0 h-[3px] {{ $tones['bar'] }}"></div>
    <div class="absolute top-4 right-4 flex size-10 items-center justify-center rounded-[10px] text-[1.1rem] {{ $tones['icon'] }}">{{ $emoji }}</div>
    <div class="text-xs font-semibold tracking-wider text-muted uppercase">{{ $label }}</div>
    <div class="text-[2rem] font-extrabold tracking-tighter">{{ $value }}</div>
    @if ($footer) <div class="text-xs text-muted">{{ $footer }}</div> @endif
    @if ($progress !== null)
        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-subtle">
            <div class="h-full rounded-full transition-[width] duration-500 {{ $tones['bar'] }}" style="width: {{ (int) $progress }}%"></div>
        </div>
    @endif
</div>
