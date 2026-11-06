<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicSessionRequest;
use App\Models\AcademicSession;
use Illuminate\Http\RedirectResponse;

class AcademicSessionController extends Controller
{
    public function store(AcademicSessionRequest $request): RedirectResponse
    {
        AcademicSession::create($request->validated());

        return $this->done('Session added successfully.');
    }

    public function update(AcademicSessionRequest $request, AcademicSession $session): RedirectResponse
    {
        $session->update($request->validated());

        return $this->done('Session updated successfully.');
    }

    public function destroy(AcademicSession $session): RedirectResponse
    {
        $session->delete();

        return $this->done('Session deleted successfully.');
    }

    private function done(string $message): RedirectResponse
    {
        return redirect()->route('settings.index', ['tab' => 'sessions'])->with('status', $message);
    }
}
