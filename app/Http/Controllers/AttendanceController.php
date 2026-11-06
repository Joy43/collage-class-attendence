<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /** The "take attendance" form. */
    public function create(): View
    {
        return view('attendance.create', [
            'courses' => Course::orderBy('title')->get(),
            'sessions' => AcademicSession::orderByDesc('year')->get(),
            'students' => Student::orderBy('roll_no')->get(),
        ]);
    }

    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $saved = $skipped = 0;

        foreach (array_filter($request->validated('attendance')) as $studentId => $status) {
            $attendance = Attendance::firstOrCreate(
                [
                    'course_id' => $request->validated('course_id'),
                    'academic_session_id' => $request->validated('academic_session_id'),
                    'student_id' => $studentId,
                    'on_date' => $request->validated('on_date'),
                ],
                ['faculty_id' => $request->user()->id, 'status' => $status],
            );

            $attendance->wasRecentlyCreated ? $saved++ : $skipped++;
        }

        if ($saved === 0) {
            return back()->withInput()->with('warning', $skipped > 0
                ? 'Attendance already recorded for this date, course, and session.'
                : 'Select a status for at least one student.');
        }

        return back()->with('status', "Attendance saved for {$saved} students successfully!"
            . ($skipped > 0 ? " ({$skipped} already existed)" : ''));
    }
}
