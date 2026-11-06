<x-layouts.app title="Dashboard">
    <x-page-header title="Dashboard" :crumbs="['Home', 'Dashboard']">
        Welcome back, <strong>{{ auth()->user()->user_name }}</strong> — {{ now()->format('l, F j, Y') }}
    </x-page-header>

    {{-- KPI tiles --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-[repeat(auto-fit,minmax(180px,1fr))]">
        <x-stat-card tone="primary" emoji="📚" label="Total Students" :value="$totalStudents" :footer="$totalCourses . ' courses active'" />
        <x-stat-card tone="present" emoji="✅" label="Present Today" :value="$today['Present']" :footer="'out of ' . $totalStudents . ' students'" :progress="$attendancePercent" />
        <x-stat-card tone="absent" emoji="❌" label="Absent Today" :value="$today['Absent']" footer="students not present" />
        <x-stat-card tone="late" emoji="⏰" label="Late Today" :value="$today['Late']" footer="arrived late" />
        <x-stat-card tone="leave" emoji="📝" label="Leave Requests" :value="$today['Leave']" footer="on leave today" />
        <x-stat-card tone="holiday" emoji="📊" label="Attendance %" :value="$attendancePercent . '%'" footer="today's overall rate" :progress="$attendancePercent" />
    </div>

    {{-- Charts (drawn by resources/js/pages/dashboard.js) --}}
    <div class="mb-6 grid grid-cols-1 gap-4 xl:grid-cols-[2fr_1fr]">
        <x-card title="📈 7-Day Attendance Trend">
            <x-slot:actions><span class="text-xs text-muted">Last 7 days</span></x-slot:actions>
            <div class="relative h-60"><canvas id="trendChart"></canvas></div>
        </x-card>
        <x-card title="🎯 Today's Overview">
            <div class="relative flex h-60 items-center justify-center"><canvas id="pieChart"></canvas></div>
        </x-card>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-card title="🔔 Recent Attendance Activity" :flush="true" class="md:col-span-2">
            <x-slot:actions>
                <x-button :href="route('reports.index')" variant="ghost" size="sm" class="text-primary!">View All →</x-button>
            </x-slot:actions>

            <div class="px-5">
                @forelse ($recent as $record)
                    <div class="flex items-start gap-3 border-b border-line py-2.5 last:border-b-0">
                        <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $record->status->bgClass() }}"></span>
                        <div>
                            <div class="text-[.825rem]">
                                <strong>{{ $record->student->name }}</strong> ({{ $record->student->roll_no }}) marked
                                <strong class="{{ $record->status->textClass() }}">{{ $record->status->value }}</strong>
                                in <em>{{ $record->course->code }}</em>
                            </div>
                            <div class="text-xs text-muted">{{ $record->on_date->format('M j, Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center text-sm text-muted">No attendance records yet.</div>
                @endforelse
            </div>
        </x-card>

        <x-card title="⚡ Quick Actions">
            <div class="flex flex-col gap-2.5">
                <x-button :href="route('attendance.create')" variant="primary" class="w-full justify-start!">Take Today's Attendance</x-button>
                <x-button :href="route('students.index')" variant="secondary" class="w-full justify-start!">Manage Student Catalog</x-button>
                <x-button :href="route('reports.index')" variant="secondary" class="w-full justify-start!">Generate Reports</x-button>
            </div>
        </x-card>
    </div>

    @php
        $chartData = [
            'trend' => $trend,
            'today' => collect(\App\Enums\AttendanceStatus::cases())->map(fn ($s) => [
                'label' => $s->value, 'count' => $today[$s->value], 'color' => $s->hex(),
            ])->values(),
        ];
    @endphp
    <script type="application/json" id="dashboard-data">{!! json_encode($chartData) !!}</script>
</x-layouts.app>
