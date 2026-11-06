{{-- Icon button that DELETEs via a confirm()ed form: <x-delete-button :action="route('students.destroy', $s)" confirm="Delete Jane?" /> --}}
@props(['action', 'confirm' => 'Are you sure?', 'label' => 'Delete'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirm))" class="inline-flex">
    @csrf
    @method('DELETE')
    <x-button variant="icon" type="submit" title="{{ $label }}" class="text-absent!"><x-icon name="trash" /></x-button>
</form>
