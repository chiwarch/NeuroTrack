<?php

namespace Tests\Feature;

use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\SkillAssessment;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruStudentAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_and_update_assessments_for_students_in_their_classes(): void
    {
        [$teacher, $student, $classSubject] = $this->createClassWithStudent();
        $otherTeacher = $this->createUser('guru');
        $this->seed(SkillSeeder::class);

        $this->actingAs($teacher)
            ->get(route('guru.students.index'))
            ->assertOk()
            ->assertSee($student->name);

        $this->actingAs($otherTeacher)
            ->get(route('guru.students.show', $student))
            ->assertForbidden();

        $skills = Skill::softSkills();
        $scores = $skills->mapWithKeys(fn (Skill $skill): array => [$skill->id => 4])->all();

        $this->actingAs($teacher)
            ->from(route('guru.students.show', $student))
            ->post(route('guru.students.rate', $student), ['scores' => $scores + [999 => 5]])
            ->assertSessionHasErrors('scores');

        $this->assertDatabaseCount('skill_assessments', 0);

        $this->actingAs($teacher)
            ->post(route('guru.students.rate', $student), ['scores' => $scores, 'notes' => 'Catatan awal'])
            ->assertRedirect();

        $assessment = SkillAssessment::with('scores')->sole();
        $this->assertSame('Catatan awal', $assessment->notes);
        $this->assertCount($skills->count(), $assessment->scores);
        $this->assertSame(4.0, $assessment->scores->first()->score);

        $updatedScores = $skills->mapWithKeys(fn (Skill $skill): array => [$skill->id => 5])->all();
        $this->actingAs($teacher)
            ->post(route('guru.students.rate', $student), ['scores' => $updatedScores])
            ->assertRedirect();

        $this->assertDatabaseCount('skill_assessments', 1);
        $this->assertSame(5.0, $assessment->fresh()->scores->first()->score);
        $this->assertSame($classSubject->class_id, $student->classes()->value('classes.id'));
    }

    /**
     * @return array{0: User, 1: User, 2: ClassSubject}
     */
    private function createClassWithStudent(): array
    {
        $teacher = $this->createUser('guru');
        $student = $this->createUser('siswa');
        $schoolClass = SchoolClass::create([
            'name' => 'X RPL 1',
            'academic_year' => '2026/2027',
            'homeroom_teacher_id' => $teacher->id,
        ]);
        $schoolClass->syncStudents([$student->id]);

        $subject = Subject::create(['name' => 'Matematika']);
        $classSubject = ClassSubject::create([
            'class_id' => $schoolClass->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ]);

        return [$teacher, $student, $classSubject];
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
