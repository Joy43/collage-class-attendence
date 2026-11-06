<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'on_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['nullable', Rule::enum(AttendanceStatus::class)],
        ];
    }
}
