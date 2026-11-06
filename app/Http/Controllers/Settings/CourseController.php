<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;

class CourseController extends Controller
{
    public function store(CourseRequest $request): RedirectResponse
    {
        Course::create($request->validated());

        return $this->done('Course added successfully.');
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return $this->done('Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return $this->done('Course deleted successfully.');
    }

    private function done(string $message): RedirectResponse
    {
        return redirect()->route('settings.index', ['tab' => 'courses'])->with('status', $message);
    }
}
