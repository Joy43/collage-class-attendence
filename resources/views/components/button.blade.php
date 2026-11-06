{{--
    <x-button variant="primary" size="sm">Save</x-button>
    <x-button href="/somewhere" variant="secondary">Link styled as button</x-button>

    variant: primary | secondary | ghost | danger | danger-soft | gradient | gradient-green | gradient-red | cancel | icon
    size:    md (default) | sm | lg
--}}
@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-primary text-white shadow-[0_4px_14px_rgba(37,99,235,0.4)] hover:bg-primary-hover hover:-translate-y-px hover:shadow-[0_6px_20px_rgba(37,99,235,0.55)]',
        'secondary' => 'border-secondary/35! bg-secondary/12 text-secondary shadow-[0_4px_14px_rgba(56,189,248,0.15)] hover:border-secondary/60! hover:bg-secondary/22 hover:text-white hover:-translate-y-px hover:shadow-[0_6px_20px_rgba(56,189,248,0.3)]',
        'ghost' => 'bg-transparent text-muted hover:border-line hover:bg-white/[0.08] hover:text-fg',
        'danger' => 'bg-absent text-white shadow-[0_4px_14px_rgba(239,68,68,0.4)] hover:bg-red-600 hover:-translate-y-px hover:shadow-[0_6px_20px_rgba(239,68,68,0.55)]',
        'danger-soft' => 'border-absent/30! bg-absent-soft text-absent hover:border-absent/60! hover:bg-absent/20 hover:-translate-y-px',
        'gradient' => 'bg-linear-to-r from-primary to-secondary text-white font-bold tracking-wide shadow-[0_4px_16px_rgba(37,99,235,0.4)] hover:-translate-y-px hover:shadow-[0_6px_24px_rgba(56,189,248,0.5)] active:scale-[.97]',
        'gradient-green' => 'bg-linear-to-r from-emerald-600 to-teal-500 text-white font-bold tracking-wide shadow-[0_4px_14px_rgba(16,185,129,0.35)] hover:-translate-y-px hover:shadow-[0_6px_20px_rgba(16,185,129,0.5)] active:scale-[.97]',
        'gradient-red' => 'bg-linear-to-r from-red-600 to-rose-500 text-white font-bold tracking-wide shadow-[0_4px_14px_rgba(239,68,68,0.35)] hover:-translate-y-px hover:shadow-[0_6px_20px_rgba(239,68,68,0.5)] active:scale-[.97]',
        'cancel' => 'border-line! bg-transparent text-muted hover:border-red-500/40! hover:bg-red-500/10 hover:text-red-400 hover:-translate-y-px active:scale-[.97]',
        'icon' => 'size-8 sm:size-9 rounded-lg border-line! bg-surface text-muted backdrop-blur-md hover:border-secondary/50! hover:bg-white/[0.12] hover:text-secondary hover:shadow-[0_0_10px_rgba(56,189,248,0.25)] [&_svg]:size-3.5 sm:[&_svg]:size-4',
    ];
    $sizes = [
        'md' => 'px-4 py-2 text-sm rounded-[12px]',
        'sm' => 'px-3 py-1.5 text-xs rounded-[10px]',
        'lg' => 'px-6 py-3 text-base rounded-[14px]',
    ];
    $classes = 'inline-flex items-center justify-center gap-2 border border-transparent font-semibold leading-snug whitespace-nowrap transition-all duration-150 active:scale-[.98] '
        . $variants[$variant] . ' ' . ($variant === 'icon' ? '' : $sizes[$size]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
