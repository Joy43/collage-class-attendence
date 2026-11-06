<x-card :flush="true">
    <x-slot:toolbar>
        <div class="text-base font-bold">Manage Faculty</div>
        <x-button variant="primary" size="sm" class="ml-auto" data-crud-create="faculty-modal-form" data-open-modal="faculty-modal">Add Faculty</x-button>
    </x-slot:toolbar>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>ID</th><th>Name</th><th>Username</th><th class="w-[100px] text-center!">Actions</th></tr></thead>
            <tbody>
                @forelse ($faculty as $member)
                    <tr>
                        <td>#{{ $member->id }}</td>
                        <td class="font-semibold">{{ $member->name }}</td>
                        <td class="text-muted">{{ $member->user_name }}</td>
                        <td>
                            <div class="flex justify-center gap-1.5">
                                <x-button variant="icon" title="Edit" data-crud-edit="faculty-modal-form"
                                          data-action="{{ route('settings.faculty.update', $member) }}"
                                          :data-values="json_encode(['name' => $member->name, 'user_name' => $member->user_name])"><x-icon name="edit" /></x-button>
                                <x-delete-button :action="route('settings.faculty.destroy', $member)" confirm="Delete this faculty member?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><x-empty-state icon="👤" title="No faculty members found" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<x-modal id="faculty-modal" title="Add Faculty" :form-action="route('settings.faculty.store')">
    <x-form.input name="name" label="Full Name" id="faculty-name" required />
    <x-form.input name="user_name" label="Username (Login ID)" id="faculty-user" required />
    <x-form.input name="password" type="password" label="Password" id="faculty-password" autocomplete="new-password" />
    <p class="text-xs text-muted" data-create-text="Minimum 6 characters." data-edit-text="Leave blank to keep the current password.">Minimum 6 characters.</p>

    <x-slot:footer>
        <x-button variant="cancel" data-close-modal>Cancel</x-button>
        <x-button type="submit" variant="gradient-green"><span data-create-text="Save Faculty" data-edit-text="Update Faculty">Save Faculty</span></x-button>
    </x-slot:footer>
</x-modal>
