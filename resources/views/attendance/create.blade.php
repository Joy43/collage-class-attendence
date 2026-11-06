<x-layouts.app title="Take Attendance">
    <x-page-header title="Take Attendance" :crumbs="['Home', 'Attendance']">
        Select a course, session, and date to record attendance.
    </x-page-header>

    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf

        <x-card title="📋 Session Details" class="mb-5">
            <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-3.5">
                <x-form.select name="course_id" label="Course" placeholder="— Select Course —" required>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->label() }}</option>
                    @endforeach
                </x-form.select>

                <x-form.select name="academic_session_id" label="Session / Term" placeholder="— Select Session —" required>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" @selected(old('academic_session_id') == $session->id)>{{ $session->label() }}</option>
                    @endforeach
                </x-form.select>

                <x-form.input type="date" name="on_date" label="Date" :value="now()->toDateString()" required />
            </div>
        </x-card>

        <x-card :flush="true">
            <x-slot:toolbar>
                <x-search-box id="student-search" placeholder="Filter students…" data-filter=".student-row" />
                <x-button variant="gradient-green" size="sm" data-mark-all="Present">All Present</x-button>
                <x-button variant="gradient-red" size="sm" data-mark-all="Absent">All Absent</x-button>
                <span class="ml-auto text-[.8rem] text-muted">{{ $students->count() }} students</span>
            </x-slot:toolbar>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-[42px]">#</th>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th class="min-w-[300px]">Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="student-row" data-search="{{ strtolower($student->name . ' ' . $student->roll_no) }}">
                                <td class="text-[.8rem] text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <x-avatar :text="$student->initial()" :index="$loop->index" />
                                        <div class="text-sm font-semibold">{{ $student->name }}</div>
                                    </div>
                                </td>
                                <td class="text-[.8rem] font-semibold text-muted">{{ $student->roll_no }}</td>
                                <td>
                                    <div data-status-row class="flex flex-wrap gap-1.5">
                                        @foreach (\App\Enums\AttendanceStatus::cases() as $status)
                                            <span data-status="{{ $status->value }}"
                                                  class="inline-flex cursor-pointer items-center gap-[5px] rounded-full border px-2.5 py-1 text-xs font-semibold whitespace-nowrap transition-all duration-150 data-[selected=true]:scale-105 data-[selected=true]:ring-2 {{ $status->chipClass() }} {{ $status->ringClass() }}">
                                                {{ $status->emoji() }} {{ $status->value }}
                                            </span>
                                        @endforeach
                                        <input type="hidden" name="attendance[{{ $student->id }}]" value="{{ old("attendance.{$student->id}") }}">
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><x-empty-state icon="👥" title="No students found">
                                <a href="{{ route('students.index') }}" class="text-primary">Add students →</a>
                            </x-empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->isNotEmpty())
                <x-slot:footer>
                    <div class="flex justify-end">
                        <x-button type="submit" variant="gradient">Save Attendance Records</x-button>
                    </div>
                </x-slot:footer>
            @endif
        </x-card>
    </form>
</x-layouts.app>
