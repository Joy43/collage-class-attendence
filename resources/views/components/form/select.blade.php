{{-- <x-form.select name="course_id" label="Course" placeholder="— Select Course —"> <option …> </x-form.select> --}}
@props(['name', 'label' => null, 'placeholder' => null])

@php $id = $attributes->get('id', $name); @endphp
<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $id }}" class="text-[.8rem] font-semibold text-fg">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" id="{{ $id }}"
        {{ $attributes->except('id')->class('select-arrow w-full cursor-pointer appearance-none rounded-[12px] border border-line bg-white/[0.04] py-2.5 pr-9 pl-3.5 text-sm text-fg outline-hidden backdrop-blur-md transition-all duration-200 focus:border-secondary focus:bg-white/[0.08] focus:ring-[3px] focus:ring-secondary/20 [&>option]:bg-[#0B1020] [&>option]:text-white') }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
    @error($name)
        <p class="text-xs text-absent">{{ $message }}</p>
    @enderror
</div>
