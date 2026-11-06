<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private const TABS = ['faculty', 'sessions', 'courses'];

    public function index(Request $request): View
    {
        $tab = in_array($request->query('tab'), self::TABS, true) ? $request->query('tab') : 'faculty';

        return view('settings.index', [
            'tab' => $tab,
            'faculty' => $tab === 'faculty' ? Faculty::orderBy('name')->get() : collect(),
            'sessions' => $tab === 'sessions' ? AcademicSession::orderByDesc('year')->orderBy('term')->get() : collect(),
            'courses' => $tab === 'courses' ? Course::orderBy('code')->get() : collect(),
        ]);
    }
}
