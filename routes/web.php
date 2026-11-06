<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Settings\AcademicSessionController;
use App\Http\Controllers\Settings\CourseController;
use App\Http\Controllers\Settings\FacultyController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

/* Guests */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
});

/* Signed-in faculty */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/reports', [AttendanceReportController::class, 'index'])->name('reports.index');

    Route::resource('students', StudentController::class)->only(['index', 'store', 'update', 'destroy']);

    // Notifications: the page (HTML) and the top-bar bell (JSON) share these routes.
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('read-all', 'markAllRead')->name('read-all');
        Route::delete('read', 'destroyRead')->name('destroy-read');
        Route::delete('/', 'destroyAll')->name('destroy-all');
        Route::patch('{notification}/read', 'markRead')->name('read');
        Route::delete('{notification}', 'destroy')->name('destroy');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::resource('faculty', FacultyController::class)->only(['store', 'update', 'destroy']);
        Route::resource('sessions', AcademicSessionController::class)->only(['store', 'update', 'destroy'])
            ->parameters(['sessions' => 'session']);
        Route::resource('courses', CourseController::class)->only(['store', 'update', 'destroy']);
    });
});
