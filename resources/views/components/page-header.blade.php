{{-- <x-page-header title="Dashboard" :crumbs="['Home', 'Dashboard']">optional subtitle</x-page-header> --}}
@props(['title', 'crumbs' => []])

<div {{ $attributes->class('mb-6') }}>
    @if ($crumbs)
        <div class="mb-1.5 flex items-center gap-1.5 text-[.8rem] text-muted">
            @foreach ($crumbs as $crumb)
                @if ($loop->last)
                    <span class="text-fg">{{ $crumb }}</span>
                @else
                    <span>{{ $crumb }}</span><span class="text-faint">›</span>
                @endif
            @endforeach
        </div>
    @endif
    <h1 class="text-2xl font-extrabold tracking-tight">{{ $title }}</h1>
    @if (! $slot->isEmpty())
        <p class="mt-0.5 text-sm text-muted">{{ $slot }}</p>
    @endif
</div>
