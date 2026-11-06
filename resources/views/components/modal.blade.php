{{--
    <x-modal id="course-modal" title="Add Course" :form-action="route('...')">
        fields…
        <x-slot:footer> buttons </x-slot:footer>
    </x-modal>
    With :form-action the content is wrapped in a POST form (csrf + a spare _method input
    that resources/js/crud-form.js switches to PUT when editing).
--}}
@props(['id', 'title', 'formAction' => null, 'maxWidth' => 'max-w-[400px]'])

<div id="{{ $id }}" data-modal class="animate-fade-in fixed inset-0 z-[9999] hidden items-center justify-center bg-black/45 p-4 backdrop-blur-sm">
    <div class="scroll-thin animate-slide-up max-h-[90vh] w-full {{ $maxWidth }} overflow-y-auto rounded-[18px] bg-surface shadow-xl">
        @if ($formAction)
            <form id="{{ $id }}-form" method="POST" action="{{ $formAction }}" data-crud data-store-action="{{ $formAction }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
        @endif

        <div class="flex items-center justify-between border-b border-line px-[22px] py-[18px]">
            <span class="text-base font-bold" data-create-text="{{ $title }}" data-edit-text="{{ str_replace('Add', 'Edit', $title) }}">{{ $title }}</span>
            <button type="button" data-close-modal class="flex size-[30px] items-center justify-center rounded-[10px] bg-subtle text-base text-muted transition-colors hover:bg-line hover:text-fg">✕</button>
        </div>

        <div class="flex flex-col gap-3 p-[22px]">{{ $slot }}</div>

        @isset($footer)
            <div class="flex justify-end gap-2 border-t border-line px-5 py-3">{{ $footer }}</div>
        @endisset

        @if ($formAction)
            </form>
        @endif
    </div>
</div>
