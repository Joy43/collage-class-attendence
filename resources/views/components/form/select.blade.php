{{-- <x-form.select name="course_id" label="Course" placeholder="— Select Course —"> <option …> </x-form.select> --}}
@props(['name', 'label' => null, 'placeholder' => null])

@php $id = $attributes->get('id', $name); @endphp
<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $id }}" class="text-[.8rem] font-semibold text-fg">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" id="{{ $id }}"
        {{ $attributes->except('id')->class('select-arrow w-full cursor-pointer appearance-none rounded-[10px] border border-line bg-surface py-2 pr-8 pl-3 text-sm text-fg outline-hidden transition focus:border-primary focus:ring-[3px] focus:ring-primary/20') }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
    @error($name)
        <p class="text-xs text-absent">{{ $message }}</p>
    @enderror
</div>
