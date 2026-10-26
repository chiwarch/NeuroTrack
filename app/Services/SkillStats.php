<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\Skill;
use App\Models\SkillAssessment;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\User;

/**
 * Data untuk dua radar chart.
 *  - Hard skill  : satu sumbu per mapel (umum + kejuruan jurusannya), nilai 0–100 = rata-rata dari
 *                  (a) nilai TERBAIK tiap kuis terbit yang sudah dikerjakan, dan
 *                  (b) nilai tiap tugas yang sudah dinilai guru (dijadikan persen dari nilai maksimal).
 *                  Setiap kuis/tugas berbobot sama. Yang belum dikerjakan/dinilai tidak dihitung,
 *                  supaya kuis atau tugas baru tidak menurunkan statistik.
 *  - Soft skill  : skala 1–5; ambil penilaian terbaru tiap guru, lalu rata-ratakan antar guru.
 * Tiap baris: ['label', 'value' (null = belum ada data), 'note'].
 */
class SkillStats
{
    public const SOFT_MAX = 5;

    public static function hardSkills(User $student): array
    {
        $subjects = Subject::where(fn ($q) => $q->where('type', 'umum')->orWhere('major_id', $student->major_id))
            ->orderByDesc('type')->orderBy('name')->get();

        $totals = Quiz::where('is_published', true)->selectRaw('subject_id, count(*) as total')
            ->groupBy('subject_id')->pluck('total', 'subject_id');

        // Nilai terbaik per kuis terbit yang sudah selesai dikerjakan.
        $best = Attempt::with('quiz:id,subject_id,is_published')
            ->where('user_id', $student->id)->where('status', 'selesai')->get()
            ->filter(fn ($a) => $a->quiz && $a->quiz->is_published)
            ->groupBy('quiz_id')
            ->map(fn ($g) => ['subject' => $g->first()->quiz->subject_id, 'score' => $g->max('score')]);

        // Tugas yang sudah dinilai, dalam persen.
        $graded = Submission::with('assignment.classSubject:id,subject_id')
            ->where('student_id', $student->id)->whereNotNull('score')->get()
            ->map(fn ($sub) => ['subject' => $sub->assignment->classSubject->subject_id, 'pct' => $sub->percent()])
            ->filter(fn ($r) => $r['pct'] !== null);

        // Jumlah tugas di kelas siswa saat ini, per mapel.
        $class = $student->classes()->first();
        $taskTotals = $class
            ? Assignment::join('class_subjects', 'class_subjects.id', '=', 'assignments.class_subject_id')
                ->where('class_subjects.class_id', $class->id)
                ->selectRaw('class_subjects.subject_id as sid, count(*) as total')
                ->groupBy('class_subjects.subject_id')->pluck('total', 'sid')
            : collect();

        return $subjects->map(function (Subject $s) use ($best, $totals, $graded, $taskTotals) {
            $quizzes = $best->where('subject', $s->id)->pluck('score');
            $tasks = $graded->where('subject', $s->id)->pluck('pct');
            $pool = $quizzes->merge($tasks);
            $quizTotal = (int) ($totals[$s->id] ?? 0);
            $taskTotal = (int) ($taskTotals[$s->id] ?? 0);

            if ($quizTotal === 0 && $taskTotal === 0) {
                $note = 'Belum ada kuis atau tugas';
            } elseif ($pool->isEmpty()) {
                $note = 'Belum ada yang dikerjakan/dinilai';
            } else {
                $note = $quizzes->count().' dari '.$quizTotal.' kuis · '.$tasks->count().' dari '.$taskTotal.' tugas dinilai';
            }

            return ['label' => $s->name, 'value' => $pool->isNotEmpty() ? round($pool->avg(), 1) : null, 'note' => $note];
        })->all();
    }

    public static function softSkills(User $student): array
    {
        $assessments = SkillAssessment::with('scores')
            ->where('student_id', $student->id)->where('source_type', 'teacher')->whereNotNull('assessed_by')
            ->orderByDesc('period_end')->orderByDesc('id')->get()
            ->unique('assessed_by')->values();

        return Skill::softSkills()->map(function (Skill $skill) use ($assessments) {
            $values = $assessments
                ->map(fn ($a) => $a->scores->firstWhere('skill_id', $skill->id)?->score)
                ->filter(fn ($v) => $v !== null);

            return [
                'label' => $skill->name,
                'value' => $values->isNotEmpty() ? round($values->avg(), 1) : null,
                'note' => $values->isNotEmpty() ? 'Dinilai '.$values->count().' guru' : 'Belum dinilai',
            ];
        })->all();
    }
}
