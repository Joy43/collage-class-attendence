<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;

class FacultyController extends Controller
{
    public function store(FacultyRequest $request): RedirectResponse
    {
        Faculty::create($request->validated());

        return $this->done('Faculty added successfully.');
    }

    public function update(FacultyRequest $request, Faculty $faculty): RedirectResponse
    {
        // A blank password field means "keep the current one".
        $faculty->update(array_filter($request->validated(), fn ($v) => $v !== null && $v !== ''));

        return $this->done('Faculty updated successfully.');
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        if ($faculty->is(auth()->user())) {
            return redirect()->route('settings.index', ['tab' => 'faculty'])
                ->withErrors(['faculty' => 'You cannot delete the account you are signed in with.']);
        }

        $faculty->delete();

        return $this->done('Faculty deleted successfully.');
    }

    private function done(string $message): RedirectResponse
    {
        return redirect()->route('settings.index', ['tab' => 'faculty'])->with('status', $message);
    }
}
