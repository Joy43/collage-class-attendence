<x-card :flush="true">
    <x-slot:toolbar>
        <div class="text-base font-bold">Manage Courses</div>
        <x-button variant="primary" size="sm" class="ml-auto" data-crud-create="course-modal-form" data-open-modal="course-modal">Add Course</x-button>
    </x-slot:toolbar>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>ID</th><th>Course Code</th><th>Title</th><th>Credits</th><th class="w-[100px] text-center!">Actions</th></tr></thead>
            <tbody>
                @forelse ($courses as $course)
                    <tr>
                        <td>#{{ $course->id }}</td>
                        <td class="font-semibold text-primary">{{ $course->code }}</td>
                        <td class="font-medium">{{ $course->title }}</td>
                        <td>{{ $course->credit }}</td>
                        <td>
                            <div class="flex justify-center gap-1.5">
                                <x-button variant="icon" title="Edit" data-crud-edit="course-modal-form"
                                          data-action="{{ route('settings.courses.update', $course) }}"
                                          :data-values="json_encode(['code' => $course->code, 'title' => $course->title, 'credit' => $course->credit])"><x-icon name="edit" /></x-button>
                                <x-delete-button :action="route('settings.courses.destroy', $course)" confirm="Delete this course?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state icon="📚" title="No courses found" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<x-modal id="course-modal" title="Add Course" :form-action="route('settings.courses.store')">
    <x-form.input name="code" label="Course Code (e.g. CS101)" id="course-code" required />
    <x-form.input name="title" label="Course Title" id="course-title" required />
    <x-form.input name="credit" type="number" label="Credits" id="course-credit" min="1" max="10" value="3" required />

    <x-slot:footer>
        <x-button variant="cancel" data-close-modal>Cancel</x-button>
        <x-button type="submit" variant="gradient-green"><span data-create-text="Save Course" data-edit-text="Update Course">Save Course</span></x-button>
    </x-slot:footer>
</x-modal>
