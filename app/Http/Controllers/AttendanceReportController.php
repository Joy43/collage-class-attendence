<?php

namespace App\Http\Controllers;
use App\Enums\AttendanceStatus;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'course_id' => ['nullable', 'integer'],
            'academic_session_id' => ['nullable', 'integer'],
            'student_id' => ['nullable', 'integer'],
            'on_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:' . implode(',', array_column(AttendanceStatus::cases(), 'value'))],
        ]);

        $records = Attendance::query()
            ->with(['student', 'course', 'academicSession', 'faculty'])
            ->when($filters['course_id'] ?? null, fn ($q, $v) => $q->where('course_id', $v))
            ->when($filters['academic_session_id'] ?? null, fn ($q, $v) => $q->where('academic_session_id', $v))
            ->when($filters['student_id'] ?? null, fn ($q, $v) => $q->where('student_id', $v))
            ->when($filters['on_date'] ?? null, fn ($q, $v) => $q->whereDate('on_date', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('on_date')
            ->orderBy(Student::select('roll_no')->whereColumn('students.id', 'attendances.student_id'))
            ->get();

        return view('reports.index', [
            'records' => $records,
            'counts' => $records->countBy(fn ($r) => $r->status->value),
            'filters' => $filters,
            'courses' => Course::orderBy('code')->get(),
            'sessions' => AcademicSession::orderByDesc('year')->get(),
            'students' => Student::orderBy('roll_no')->get(),
        ]);
    }
}
