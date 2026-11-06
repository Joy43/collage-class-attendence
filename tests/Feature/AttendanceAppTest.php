<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceAppTest extends TestCase
{
    use RefreshDatabase;

    private function faculty(): Faculty
    {
        return Faculty::create(['user_name' => 'tester', 'name' => 'Test Faculty', 'password' => 'secret123']);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_faculty_can_sign_in_with_hashed_password(): void
    {
        $faculty = $this->faculty();

        $this->post('/login', ['user_name' => 'tester', 'password' => 'wrong'])->assertSessionHasErrors('user_name');
        $this->post('/login', ['user_name' => 'tester', 'password' => 'secret123'])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($faculty);
        $this->assertNotSame('secret123', $faculty->fresh()->password);
    }

    public function test_every_page_renders_for_a_signed_in_faculty(): void
    {
        $this->actingAs($this->faculty());

        foreach (['/dashboard', '/attendance', '/reports', '/students', '/notifications', '/settings',
                  '/settings?tab=sessions', '/settings?tab=courses'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_student_crud_records_notifications(): void
    {
        $faculty = $this->faculty();
        $this->actingAs($faculty);

        $this->post('/students', ['roll_no' => 'R1', 'name' => 'Jane'])->assertSessionHasNoErrors();
        $student = Student::firstWhere('roll_no', 'R1');

        $this->post('/students', ['roll_no' => 'R1', 'name' => 'Dup'])->assertSessionHasErrors('roll_no');
        $this->put("/students/{$student->id}", ['roll_no' => 'R1', 'name' => 'Jane Doe'])->assertSessionHasNoErrors();
        $this->delete("/students/{$student->id}");

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertSame(3, $faculty->activityNotifications()->count());
    }

    public function test_attendance_is_saved_once_per_student_course_session_and_day(): void
    {
        $this->actingAs($this->faculty());
        $student = Student::create(['roll_no' => 'R1', 'name' => 'Jane']);
        $course = Course::create(['code' => 'C1', 'title' => 'Course', 'credit' => 3]);
        $session = AcademicSession::create(['year' => 2026, 'term' => 'SPRING']);

        $payload = [
            'course_id' => $course->id, 'academic_session_id' => $session->id, 'on_date' => '2026-11-06',
            'attendance' => [$student->id => 'Present'],
        ];

        $this->post('/attendance', $payload)->assertSessionHas('status');
        $this->post('/attendance', $payload)->assertSessionHas('warning');

        $this->assertSame(1, Attendance::count());
    }

    public function test_notifications_json_is_scoped_to_the_signed_in_faculty(): void
    {
        $me = $this->faculty();
        $other = Faculty::create(['user_name' => 'other', 'name' => 'Other', 'password' => 'secret123']);
        $other->activityNotifications()->create(['type' => 'create', 'title' => 'x', 'message' => 'y']);

        $this->actingAs($me)->getJson('/notifications')->assertOk()->assertJson(['unread' => 0]);
        $this->patchJson('/notifications/' . $other->activityNotifications()->first()->id . '/read')->assertForbidden();
    }
}
