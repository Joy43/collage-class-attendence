{{-- <x-avatar text="A" size="size-9 text-[.8rem]" :index="3" /> — gradient rotates with :index --}}
@props(['text', 'size' => 'size-[34px] text-[.8rem]', 'index' => 0])

@php
    $gradients = [
        'from-indigo-500 to-blue-600',
        'from-amber-500 to-red-500',
        'from-emerald-500 to-sky-600',
        'from-violet-600 to-pink-500',
        'from-teal-500 to-cyan-500',
        'from-orange-500 to-amber-400',
    ];
@endphp

<div {{ $attributes->class(['flex shrink-0 items-center justify-center rounded-full bg-linear-to-br font-bold text-white', $size, $gradients[$index % count($gradients)]]) }}>{{ $text }}</div>
