{{-- Dashboard KPI tile. tone: primary | present | absent | late | leave | holiday --}}
@props(['tone' => 'primary', 'emoji', 'label', 'value', 'footer' => null, 'progress' => null])

@php
    $tones = [
        'primary' => ['bar' => 'bg-linear-to-r from-primary to-secondary shadow-[0_0_12px_rgba(56,189,248,0.5)]', 'icon' => 'bg-primary-soft text-secondary border border-secondary/30 shadow-[0_0_15px_rgba(56,189,248,0.2)]'],
        'present' => ['bar' => 'bg-present shadow-[0_0_12px_rgba(16,185,129,0.5)]', 'icon' => 'bg-present-soft text-present border border-present/30 shadow-[0_0_15px_rgba(16,185,129,0.2)]'],
        'absent' => ['bar' => 'bg-absent shadow-[0_0_12px_rgba(239,68,68,0.5)]', 'icon' => 'bg-absent-soft text-absent border border-absent/30 shadow-[0_0_15px_rgba(239,68,68,0.2)]'],
        'late' => ['bar' => 'bg-late shadow-[0_0_12px_rgba(245,158,11,0.5)]', 'icon' => 'bg-late-soft text-late border border-late/30 shadow-[0_0_15px_rgba(245,158,11,0.2)]'],
        'leave' => ['bar' => 'bg-leave shadow-[0_0_12px_rgba(168,85,247,0.5)]', 'icon' => 'bg-leave-soft text-leave border border-leave/30 shadow-[0_0_15px_rgba(168,85,247,0.2)]'],
        'holiday' => ['bar' => 'bg-secondary shadow-[0_0_12px_rgba(56,189,248,0.5)]', 'icon' => 'bg-secondary-soft text-secondary border border-secondary/30 shadow-[0_0_15px_rgba(56,189,248,0.2)]'],
    ][$tone];
@endphp

<div class="group relative flex flex-col gap-3 overflow-hidden rounded-[16px] border border-line bg-surface p-5 backdrop-blur-xl shadow-[0_8px_32px_0_rgba(0,0,0,0.37)] transition-all duration-200 hover:-translate-y-1 hover:border-secondary/40 hover:shadow-[0_12px_36px_rgba(56,189,248,0.15)]">
    <div class="absolute inset-x-0 top-0 h-[3px] {{ $tones['bar'] }}"></div>
    <div class="absolute top-4 right-4 flex size-11 items-center justify-center rounded-[12px] text-[1.2rem] backdrop-blur-md transition-transform duration-200 group-hover:scale-110 {{ $tones['icon'] }}">{{ $emoji }}</div>
    <div class="text-[.72rem] font-bold tracking-wider text-muted uppercase">{{ $label }}</div>
    <div class="text-[2.1rem] font-black tracking-tight text-fg">{{ $value }}</div>
    @if ($footer) <div class="text-xs text-muted">{{ $footer }}</div> @endif
    @if ($progress !== null)
        <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-white/10">
            <div class="h-full rounded-full transition-[width] duration-500 {{ $tones['bar'] }}" style="width: {{ (int) $progress }}%"></div>
        </div>
    @endif
</div>
