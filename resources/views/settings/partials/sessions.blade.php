<x-card :flush="true">
    <x-slot:toolbar>
        <div class="text-base font-bold">Manage Sessions</div>
        <x-button variant="primary" size="sm" class="ml-auto" data-crud-create="session-modal-form" data-open-modal="session-modal">Add Session</x-button>
    </x-slot:toolbar>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>ID</th><th>Year</th><th>Term</th><th class="w-[100px] text-center!">Actions</th></tr></thead>
            <tbody>
                @forelse ($sessions as $session)
                    <tr>
                        <td>#{{ $session->id }}</td>
                        <td class="font-semibold">{{ $session->year }}</td>
                        <td><span class="rounded-md bg-subtle px-2 py-1 text-xs font-semibold">{{ $session->term }}</span></td>
                        <td>
                            <div class="flex justify-center gap-1.5">
                                <x-button variant="icon" title="Edit" data-crud-edit="session-modal-form"
                                          data-action="{{ route('settings.sessions.update', $session) }}"
                                          :data-values="json_encode(['year' => $session->year, 'term' => $session->term])"><x-icon name="edit" /></x-button>
                                <x-delete-button :action="route('settings.sessions.destroy', $session)" confirm="Delete this session?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><x-empty-state icon="📅" title="No sessions found" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<x-modal id="session-modal" title="Add Session" :form-action="route('settings.sessions.store')">
    <x-form.input name="year" type="number" label="Year" id="session-year" min="2000" max="2100" :value="date('Y')" required />
    <x-form.input name="term" label="Term (e.g. Spring Semester)" id="session-term" required />

    <x-slot:footer>
        <x-button variant="cancel" data-close-modal>Cancel</x-button>
        <x-button type="submit" variant="gradient-green"><span data-create-text="Save Session" data-edit-text="Update Session">Save Session</span></x-button>
    </x-slot:footer>
</x-modal>
