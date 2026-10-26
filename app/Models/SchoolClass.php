<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/** Tabel `classes` (nama "Class" dihindari karena kata cadangan PHP). */
class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'academic_year', 'homeroom_teacher_id'];

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /** Siswa aktif (belum keluar dari kelas). */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_memberships', 'class_id', 'student_id')
            ->wherePivotNull('left_at');
    }

    public function classSubjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    /**
     * Samakan guru pengampu per mapel di kelas ini. $map = [subject_id => teacher_id|null].
     * Mapel yang sudah punya tugas tidak dilepas. Mengembalikan nama mapel yang dilewati.
     * Guru yang ditugaskan otomatis ikut terdaftar pada mapel itu (subject_teacher, dipakai untuk membuat kuis).
     */
    public function syncSubjects(array $map): array
    {
        $skipped = [];

        foreach (Subject::all(['id', 'name']) as $subject) {
            $teacherId = (int) ($map[$subject->id] ?? 0);
            $row = ClassSubject::where('class_id', $this->id)->where('subject_id', $subject->id)->first();

            if ($teacherId) {
                ClassSubject::updateOrCreate(
                    ['class_id' => $this->id, 'subject_id' => $subject->id],
                    ['teacher_id' => $teacherId],
                );
                User::find($teacherId)?->subjects()->syncWithoutDetaching([$subject->id]);
            } elseif ($row) {
                $row->assignments()->exists() ? $skipped[] = $subject->name : $row->delete();
            }
        }

        return $skipped;
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'class_id');
    }

    /**
     * Samakan daftar siswa aktif dengan $ids. Yang keluar diberi left_at (riwayat tetap ada);
     * siswa yang dipindah ke kelas ini otomatis keluar dari kelas lamanya.
     */
    public function syncStudents(array $ids): void
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $now = now();
        $active = DB::table('class_memberships')->where('class_id', $this->id)->whereNull('left_at')->pluck('student_id')->map(fn ($v) => (int) $v)->all();

        $leaving = array_diff($active, $ids);
        if ($leaving) {
            DB::table('class_memberships')->where('class_id', $this->id)->whereNull('left_at')
                ->whereIn('student_id', $leaving)->update(['left_at' => $now, 'updated_at' => $now]);
        }

        $joining = array_diff($ids, $active);
        if ($joining) {
            DB::table('class_memberships')->where('class_id', '!=', $this->id)->whereNull('left_at')
                ->whereIn('student_id', $joining)->update(['left_at' => $now, 'updated_at' => $now]);
            foreach ($joining as $sid) {
                DB::table('class_memberships')->insert([
                    'class_id' => $this->id, 'student_id' => $sid,
                    'joined_at' => $now, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }
}
