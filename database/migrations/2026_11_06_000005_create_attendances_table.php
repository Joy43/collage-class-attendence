<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Keep the record if the faculty member who took it is removed.
            $table->foreignId('faculty_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('on_date');
            $table->string('status', 10); // see App\Enums\AttendanceStatus
            $table->timestamps();

            // One mark per student per course/session/day.
            $table->unique(['course_id', 'academic_session_id', 'student_id', 'on_date'], 'attendance_unique_mark');
            $table->index('on_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
