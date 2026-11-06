<x-layouts.app title="Students">
    <x-page-header title="Student Management" :crumbs="['Home', 'Students']">
        {{ $students->count() }} students registered
    </x-page-header>

    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[1fr_340px]">

        {{-- Directory --}}
        <x-card :flush="true">
            <x-slot:toolbar>
                <x-search-box id="student-filter" placeholder="Search students…" data-filter=".student-row" />
                <span class="ml-auto text-[.8rem] text-muted">{{ $students->count() }} total</span>
            </x-slot:toolbar>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-11"></th><th>Student</th><th>Roll No</th><th>Attendance</th>
                            <th class="w-[100px] text-center!">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            @php
                                $percent = $student->attendancePercent();
                                [$text, $fill] = $percent >= 75 ? ['text-present', 'bg-present'] : ($percent >= 50 ? ['text-late', 'bg-late'] : ['text-absent', 'bg-absent']);
                                $profile = [
                                    'id' => $student->id, 'name' => $student->name, 'roll_no' => $student->roll_no,
                                    'initial' => $student->initial(), 'percent' => $percent,
                                    'total' => $student->attendances_count, 'present' => $student->present_count,
                                    'gradient' => ['from-indigo-500 to-blue-600', 'from-amber-500 to-red-500', 'from-emerald-500 to-sky-600', 'from-violet-600 to-pink-500', 'from-teal-500 to-cyan-500', 'from-orange-500 to-amber-400'][$loop->index % 6],
                                ];
                            @endphp
                            <tr class="student-row" data-search="{{ strtolower($student->name . ' ' . $student->roll_no) }}">
                                <td>
                                    <button type="button" data-profile='@json($profile)'>
                                        <x-avatar :text="$student->initial()" size="size-9 text-[.8rem]" :index="$loop->index" />
                                    </button>
                                </td>
                                <td>
                                    <div class="text-sm font-semibold">{{ $student->name }}</div>
                                    <div class="text-xs text-muted">ID: {{ $student->id }}</div>
                                </td>
                                <td class="text-[.85rem] font-semibold">{{ $student->roll_no }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="h-1 grow overflow-hidden rounded-full bg-line">
                                            <div class="h-full rounded-full transition-[width] duration-500 {{ $fill }}" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="min-w-9 text-xs font-bold {{ $text }}">{{ $percent }}%</span>
                                    </div>
                                    <div class="mt-0.5 text-[.7rem] text-muted">{{ $student->attendances_count }} sessions</div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1.5">
                                        <x-button variant="icon" title="View Profile" :data-profile="json_encode($profile)"><x-icon name="eye" /></x-button>
                                        <x-button variant="icon" title="Edit" data-crud-edit="student-form" data-student="{{ $student->id }}"
                                                  data-action="{{ route('students.update', $student) }}"
                                                  :data-values="json_encode(['roll_no' => $student->roll_no, 'name' => $student->name])"><x-icon name="edit" /></x-button>
                                        <x-delete-button :action="route('students.destroy', $student)" :confirm="'Delete ' . $student->name . '? This removes all their attendance records.'" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-empty-state icon="👥" title="No students yet">Register your first student using the form.</x-empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Create / edit form (mode switched by resources/js/crud-form.js) --}}
        <x-card :flush="true" id="form-card" data-crud-scope>
            <div class="border-b border-line bg-linear-to-br from-primary/5 to-leave/5 px-5 py-4">
                <div class="text-[.95rem] font-bold" data-create-text="Register Student" data-edit-text="✏️ Edit Student">Register Student</div>
                <div class="mt-px text-xs text-muted" data-create-text="Fill in the details to add a new student" data-edit-text="Update the student record below">Fill in the details to add a new student</div>
            </div>

            <form id="student-form" method="POST" action="{{ route('students.store') }}" data-crud data-store-action="{{ route('students.store') }}" class="group p-5">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>

                <div class="mb-4"><x-form.input name="roll_no" label="🪪 Roll Number" placeholder="e.g. CSB21011" required /></div>
                <div class="mb-6"><x-form.input name="name" label="👤 Full Name" placeholder="e.g. John Doe" required /></div>

                <div class="flex items-center gap-2">
                    <x-button type="submit" variant="gradient" class="w-full group-data-[mode=edit]:from-violet-500 group-data-[mode=edit]:to-violet-600">
                        <span data-create-text="Register Student" data-edit-text="Update Profile">Register Student</span>
                    </x-button>
                    <x-button variant="cancel" data-crud-create="student-form">Cancel</x-button>
                </div>
            </form>
        </x-card>
    </div>

    {{-- Profile modal --}}
    <div id="profile-modal" data-modal class="animate-fade-in fixed inset-0 z-[9999] hidden items-center justify-center bg-black/45 p-4 backdrop-blur-sm">
        <div class="scroll-thin animate-slide-up max-h-[90vh] w-full max-w-[480px] overflow-y-auto rounded-[18px] bg-surface shadow-xl">
            <div class="flex items-center justify-between border-b border-line px-[22px] py-[18px]">
                <span class="text-base font-bold">Student Profile</span>
                <button type="button" data-close-modal class="flex size-[30px] items-center justify-center rounded-[10px] bg-subtle text-muted hover:bg-line hover:text-fg">✕</button>
            </div>
            <div class="p-[22px]">
                <div id="profile-summary" class="flex flex-col items-center gap-2 px-4 py-6 text-center"></div>
                <div id="profile-rows"></div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line px-5 py-3">
                <x-button variant="secondary" size="sm" data-close-modal>Close</x-button>
                <x-button variant="primary" size="sm" id="profile-edit-btn">Edit</x-button>
            </div>
        </div>
    </div>
</x-layouts.app>
