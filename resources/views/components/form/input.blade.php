{{-- <x-form.input name="roll_no" label="Roll Number" placeholder="e.g. CSB21011" required /> --}}
@props(['name', 'label' => null, 'type' => 'text', 'value' => null])

@php $id = $attributes->get('id', $name); @endphp
<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $id }}" class="text-[.8rem] font-semibold text-fg">{{ $label }}</label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ old($name, $value) }}"
        {{ $attributes->except('id')->class('w-full rounded-[10px] border border-line bg-surface px-3 py-2 text-sm text-fg outline-hidden transition focus:border-primary focus:ring-[3px] focus:ring-primary/20') }}>
    @error($name)
        <p class="text-xs text-absent">{{ $message }}</p>
    @enderror
</div>
