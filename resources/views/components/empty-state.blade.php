{{-- <x-empty-state icon="👥" title="No students yet">Helpful hint</x-empty-state> --}}
@props(['icon' => '📭', 'title'])

<div {{ $attributes->class('px-6 py-14 text-center text-sm text-muted') }}>
    <div class="mb-3 text-[2.5rem]">{{ $icon }}</div>
    <div class="mb-1.5 text-base font-bold text-fg">{{ $title }}</div>
    @if (! $slot->isEmpty()) <p>{{ $slot }}</p> @endif
</div>
