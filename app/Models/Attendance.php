<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public const LABELS = [
        'present' => 'Hadir',
        'late' => 'Terlambat',
        'sick' => 'Sakit',
        'permission' => 'Izin',
        'absent' => 'Alfa',
        'dispensation' => 'Dispensasi',
    ];

    public const TONES = [
        'present' => 'bg-emerald-100 text-emerald-700',
        'late' => 'bg-amber-100 text-amber-700',
        'sick' => 'bg-sky-100 text-sky-700',
        'permission' => 'bg-violet-100 text-violet-700',
        'absent' => 'bg-rose-100 text-rose-700',
        'dispensation' => 'bg-slate-100 text-slate-600',
    ];

    protected $fillable = [
        'student_id', 'class_id', 'recorded_by', 'attendance_session_id',
        'attendance_date', 'status', 'check_in_at', 'method', 'notes',
    ];

    protected function casts(): array
    {
        return ['attendance_date' => 'date', 'check_in_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function label(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    public function tone(): string
    {
        return self::TONES[$this->status] ?? 'bg-slate-100 text-slate-600';
    }
}
