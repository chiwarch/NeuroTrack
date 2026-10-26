<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\User;
use App\Services\SkillStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_grade_submissions_without_clearing_unsubmitted_grade_fields(): void
    {
        [$teacher, $firstStudent, $assignment] = $this->createAssignment();
        $secondStudent = $this->createUser('siswa');
        $assignment->classSubject->schoolClass->syncStudents([$firstStudent->id, $secondStudent->id]);

        $firstSubmission = $assignment->submissions()->create([
            'student_id' => $firstStudent->id,
            'answer_text' => 'Jawaban pertama',
            'submitted_at' => now(),
        ]);
        $secondSubmission = $assignment->submissions()->create([
            'student_id' => $secondStudent->id,
            'answer_text' => 'Jawaban kedua',
            'submitted_at' => now(),
            'score' => 70,
            'feedback' => 'Pertahankan.',
            'graded_by' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('guru.assignments.show', $assignment))
            ->assertOk()
            ->assertSee($firstStudent->name)
            ->assertSee($secondStudent->name);

        $this->actingAs($teacher)
            ->post(route('guru.assignments.grade', $assignment), [
                'scores' => [$firstSubmission->id => 88.5],
                'feedback' => [$firstSubmission->id => 'Bagus.'],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('submissions', [
            'id' => $firstSubmission->id,
            'score' => 88.5,
            'feedback' => 'Bagus.',
            'graded_by' => $teacher->id,
        ]);
        $this->assertDatabaseHas('submissions', [
            'id' => $secondSubmission->id,
            'score' => 70,
            'feedback' => 'Pertahankan.',
            'graded_by' => $teacher->id,
        ]);
    }

    public function test_student_can_submit_text_and_cannot_resubmit_after_grading(): void
    {
        [$teacher, $student, $assignment] = $this->createAssignment();
        $assignment->classSubject->schoolClass->syncStudents([$student->id]);

        $this->actingAs($student)
            ->get(route('siswa.assignments.show', $assignment))
            ->assertOk()
            ->assertSee($assignment->title);

        $this->actingAs($student)
            ->post(route('siswa.assignments.submit', $assignment), ['answer_text' => 'Jawaban tugas'])
            ->assertRedirect(route('siswa.assignments.show', $assignment));

        $submission = Submission::sole();
        $this->assertSame('Jawaban tugas', $submission->answer_text);
        $this->assertNotNull($submission->submitted_at);

        $this->actingAs($teacher)
            ->post(route('guru.assignments.grade', $assignment), [
                'scores' => [$submission->id => 95],
            ])
            ->assertRedirect();

        $this->actingAs($student)
            ->post(route('siswa.assignments.submit', $assignment), ['answer_text' => 'Jawaban berubah'])
            ->assertRedirect()
            ->assertSessionHas('error', 'Tugas sudah dinilai, tidak bisa dikumpulkan ulang.');

        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'answer_text' => 'Jawaban tugas',
            'score' => 95,
        ]);
        $this->assertSame(95.0, SkillStats::hardSkills($student)[0]['value']);
    }

    /**
     * @return array{0: User, 1: User, 2: Assignment}
     */
    private function createAssignment(): array
    {
        $teacher = $this->createUser('guru');
        $student = $this->createUser('siswa');
        $schoolClass = SchoolClass::create([
            'name' => 'X RPL 1',
            'academic_year' => '2026/2027',
            'homeroom_teacher_id' => $teacher->id,
        ]);
        $subject = Subject::create(['name' => 'Matematika']);
        $classSubject = ClassSubject::create([
            'class_id' => $schoolClass->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ]);
        $schoolClass->syncStudents([$student->id]);
        $assignment = Assignment::create([
            'class_subject_id' => $classSubject->id,
            'created_by' => $teacher->id,
            'title' => 'Latihan Matematika',
            'max_score' => 100,
        ]);

        return [$teacher, $student, $assignment];
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => 'Test '.ucfirst($role),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
