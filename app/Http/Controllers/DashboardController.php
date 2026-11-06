<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalStudents = Student::count();

        // Today's headcount per status.
        $today = Attendance::query()
            ->whereDate('on_date', today())
            ->selectRaw('status, COUNT(DISTINCT student_id) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $countFor = fn (AttendanceStatus $status) => (int) ($today[$status->value] ?? 0);
        $presentToday = $countFor(AttendanceStatus::Present);
        $attendancePercent = $totalStudents > 0 ? (int) round($presentToday / $totalStudents * 100) : 0;

        return view('dashboard', [
            'totalStudents' => $totalStudents,
            'totalCourses' => Course::count(),
            'today' => collect(AttendanceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $countFor($s)]),
            'attendancePercent' => $attendancePercent,
            'trend' => $this->lastSevenDays(),
            'recent' => Attendance::with(['student', 'course'])->latest('on_date')->latest('id')->limit(5)->get(),
        ]);
    }

    /** Present/absent headcounts for each of the last 7 days (oldest first). */
    private function lastSevenDays(): array
    {
        $days = collect(range(6, 0))->map(fn ($ago) => today()->subDays($ago));

        $rows = Attendance::query()
            ->whereBetween('on_date', [$days->first(), $days->last()])
            ->whereIn('status', [AttendanceStatus::Present->value, AttendanceStatus::Absent->value])
            ->selectRaw('on_date, status, COUNT(DISTINCT student_id) AS total')
            ->groupBy('on_date', 'status')
            ->get()
            ->groupBy(fn ($row) => $row->on_date->toDateString());

        $countOn = fn ($day, AttendanceStatus $status) => (int) ($rows[$day->toDateString()] ?? collect())
            ->firstWhere('status', $status)?->total;

        return [
            'labels' => $days->map->format('D')->all(),
            'present' => $days->map(fn ($d) => $countOn($d, AttendanceStatus::Present))->all(),
            'absent' => $days->map(fn ($d) => $countOn($d, AttendanceStatus::Absent))->all(),
        ];
    }
}
