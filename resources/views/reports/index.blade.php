<x-layouts.app title="Attendance Reports">
    <x-page-header title="Attendance Reports" :crumbs="['Home', 'Reports']">
        Filter, browse, and export attendance records.
    </x-page-header>

    {{-- Summary tiles --}}
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-[repeat(auto-fit,minmax(130px,1fr))]">
        @foreach (\App\Enums\AttendanceStatus::cases() as $status)
            <div class="flex items-center gap-3 rounded-xl border border-l-[3px] border-line bg-surface p-4 shadow-sm {{ $status->borderClass() }}">
                <span class="text-[1.2rem]">{{ $status->emoji() }}</span>
                <div>
                    <div class="text-[1.4rem] font-extrabold tracking-tighter {{ $status->textClass() }}">{{ $counts[$status->value] ?? 0 }}</div>
                    <div class="text-[.7rem] font-semibold tracking-wider text-muted uppercase">{{ $status->value }}</div>
                </div>
            </div>
        @endforeach
        <div class="flex items-center gap-3 rounded-xl border border-l-[3px] border-l-primary border-line bg-surface p-4 shadow-sm">
            <span class="text-[1.2rem]">📋</span>
            <div>
                <div class="text-[1.4rem] font-extrabold tracking-tighter text-primary">{{ $records->count() }}</div>
                <div class="text-[.7rem] font-semibold tracking-wider text-muted uppercase">Total</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <x-card title="🔎 Filter Records" class="mb-5">
        <x-slot:actions><x-button :href="route('reports.index')" variant="ghost" size="sm">Clear Filters</x-button></x-slot:actions>

        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-3.5">
            <x-form.select name="course_id" label="Course" placeholder="All Courses">
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected(($filters['course_id'] ?? null) == $course->id)>{{ $course->label() }}</option>
                @endforeach
            </x-form.select>
            <x-form.select name="academic_session_id" label="Session" placeholder="All Sessions">
                @foreach ($sessions as $session)
                    <option value="{{ $session->id }}" @selected(($filters['academic_session_id'] ?? null) == $session->id)>{{ $session->label() }}</option>
                @endforeach
            </x-form.select>
            <x-form.select name="student_id" label="Student" placeholder="All Students">
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected(($filters['student_id'] ?? null) == $student->id)>{{ $student->roll_no }} — {{ $student->name }}</option>
                @endforeach
            </x-form.select>
            <x-form.input type="date" name="on_date" label="Date" :value="$filters['on_date'] ?? null" />
            <x-form.select name="status" label="Status" placeholder="All Statuses">
                @foreach (\App\Enums\AttendanceStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->emoji() }} {{ $status->value }}</option>
                @endforeach
            </x-form.select>
            <div class="flex items-end"><x-button type="submit" variant="gradient" class="w-full">Apply Filters</x-button></div>
        </form>
    </x-card>

    {{-- Results --}}
    <x-card :flush="true">
        <x-slot:toolbar>
            <x-search-box id="report-search" placeholder="Search name, roll, course…" data-filter=".report-row" />
            <x-button id="export-csv" variant="secondary" size="sm" data-date="{{ now()->toDateString() }}">Export CSV</x-button>
            <span class="ml-auto text-[.8rem] text-muted">{{ $records->count() }} records</span>
        </x-slot:toolbar>

        <div class="overflow-x-auto">
            <table id="report-table" class="data-table">
                <thead>
                    <tr>
                        <th>#</th><th>Student</th><th>Roll No</th><th>Course</th>
                        <th>Session</th><th>Date</th><th>Status</th><th>Faculty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr class="report-row" data-search="{{ strtolower("{$record->student->name} {$record->student->roll_no} {$record->course->code} {$record->status->value}") }}">
                            <td class="text-[.8rem] text-faint">{{ $loop->iteration }}</td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <x-avatar :text="$record->student->initial()" size="size-[30px] text-[.72rem]" />
                                    <span class="text-[.85rem] font-semibold">{{ $record->student->name }}</span>
                                </div>
                            </td>
                            <td class="text-[.8rem] font-semibold">{{ $record->student->roll_no }}</td>
                            <td>
                                <div class="text-[.8rem] font-semibold">{{ $record->course->code }}</div>
                                <div class="text-[.72rem] text-muted">{{ $record->course->title }}</div>
                            </td>
                            <td class="text-[.8rem]">{{ $record->academicSession->year }} – {{ $record->academicSession->term }}</td>
                            <td class="text-[.8rem] whitespace-nowrap">{{ $record->on_date->format('M j, Y') }}</td>
                            <td><x-status-chip :status="$record->status" /></td>
                            <td class="text-[.8rem] text-muted">{{ $record->faculty?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state icon="📊" title="No records found">
                            Try adjusting your filters or <a href="{{ route('attendance.create') }}" class="text-primary">record attendance</a>.
                        </x-empty-state></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
