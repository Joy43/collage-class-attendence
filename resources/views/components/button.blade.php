{{--
    <x-button variant="primary" size="sm">Save</x-button>
    <x-button href="/somewhere" variant="secondary">Link styled as button</x-button>

    variant: primary | secondary | ghost | danger | danger-soft | gradient | gradient-green | gradient-red | cancel | icon
    size:    md (default) | sm | lg
--}}
@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-primary text-white shadow-sm hover:bg-primary-hover hover:-translate-y-px hover:shadow-md',
        'secondary' => 'bg-surface text-fg border-line! shadow-sm hover:bg-subtle hover:border-line-hover! hover:-translate-y-px',
        'ghost' => 'bg-transparent text-muted hover:bg-subtle hover:text-fg',
        'danger' => 'bg-absent text-white hover:bg-red-700 hover:-translate-y-px',
        'danger-soft' => 'bg-absent-soft text-absent border-absent/20! hover:-translate-y-px',
        'gradient' => 'bg-linear-to-b from-blue-500 to-blue-600 text-white font-bold tracking-wide shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_2px_8px_rgba(37,99,235,.4)] hover:-translate-y-px hover:shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_6px_16px_rgba(37,99,235,.5)] active:scale-[.96]',
        'gradient-green' => 'bg-linear-to-b from-green-500 to-green-600 text-white font-bold tracking-wide shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_2px_8px_rgba(22,163,74,.4)] hover:-translate-y-px active:scale-[.96]',
        'gradient-red' => 'bg-linear-to-b from-red-500 to-red-600 text-white font-bold tracking-wide shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_2px_8px_rgba(220,38,38,.4)] hover:-translate-y-px active:scale-[.96]',
        'cancel' => 'bg-transparent text-muted border-line! hover:border-red-500/30! hover:text-red-500 hover:bg-red-500/10 hover:-translate-y-px active:scale-[.96]',
        'icon' => 'size-[30px] rounded-md border-line! bg-surface text-muted hover:bg-subtle hover:text-fg [&_svg]:size-3.5',
    ];
    $sizes = [
        'md' => 'px-4 py-2 text-sm rounded-[10px]',
        'sm' => 'px-2.5 py-[5px] text-[.8rem] rounded-md',
        'lg' => 'px-6 py-3 text-base rounded-[10px]',
    ];
    $classes = 'inline-flex items-center justify-center gap-[7px] border border-transparent font-semibold leading-snug whitespace-nowrap transition-all duration-150 '
        . $variants[$variant] . ' ' . ($variant === 'icon' ? '' : $sizes[$size]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
