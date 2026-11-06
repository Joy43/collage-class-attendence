<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Http\Requests\StudentRequest;
use App\Models\ActivityNotification;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::query()
            ->withCount([
                'attendances',
                'attendances as present_count' => fn ($q) => $q->where('status', 'Present'),
            ])
            ->orderBy('roll_no')
            ->get();

        return view('students.index', compact('students'));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $student = Student::create($request->validated());

        ActivityNotification::record(NotificationType::Create, '➕ New Student Registered',
            "{$student->name} (Roll: {$student->roll_no}) has been added to the system.");

        return back()->with('status', 'Student registered successfully!');
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        ActivityNotification::record(NotificationType::Update, '✏️ Student Updated',
            "Profile of {$student->name} (Roll: {$student->roll_no}) was updated.");

        return back()->with('status', 'Student updated successfully!');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete(); // attendance rows are removed by the foreign key cascade

        ActivityNotification::record(NotificationType::Delete, '🗑️ Student Deleted',
            "{$student->name} (Roll: {$student->roll_no}) was removed from the system.");

        return back()->with('status', 'Student and attendance records deleted.');
    }
}
