{{--
    <x-modal id="course-modal" title="Add Course" :form-action="route('...')">
        fields…
        <x-slot:footer> buttons </x-slot:footer>
    </x-modal>
    With :form-action the content is wrapped in a POST form (csrf + a spare _method input
    that resources/js/crud-form.js switches to PUT when editing).
--}}
@props(['id', 'title', 'formAction' => null, 'maxWidth' => 'max-w-[420px]'])

<div id="{{ $id }}" data-modal class="animate-fade-in fixed inset-0 z-[9999] hidden items-center justify-center bg-black/65 p-4 backdrop-blur-md">
    <div class="scroll-thin animate-slide-up max-h-[90vh] w-full {{ $maxWidth }} overflow-y-auto rounded-[20px] border border-line bg-[#0B1020]/95 shadow-[0_20px_50px_rgba(0,0,0,0.6)] backdrop-blur-2xl">
        @if ($formAction)
            <form id="{{ $id }}-form" method="POST" action="{{ $formAction }}" data-crud data-store-action="{{ $formAction }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
        @endif

        <div class="flex items-center justify-between border-b border-line px-6 py-5">
            <span class="text-base font-bold text-fg" data-create-text="{{ $title }}" data-edit-text="{{ str_replace('Add', 'Edit', $title) }}">{{ $title }}</span>
            <button type="button" data-close-modal class="flex size-8 items-center justify-center rounded-xl border border-line bg-surface text-sm text-muted transition-colors hover:border-secondary/40 hover:bg-white/[0.12] hover:text-fg">✕</button>
        </div>

        <div class="flex flex-col gap-4 p-6">{{ $slot }}</div>

        @isset($footer)
            <div class="flex justify-end gap-2.5 border-t border-line bg-subtle/40 px-6 py-4">{{ $footer }}</div>
        @endisset

        @if ($formAction)
            </form>
        @endif
    </div>
</div>
