<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AttendanceSession extends Model
{
    protected $fillable = ['class_id', 'teacher_id', 'attendance_date', 'late_after', 'closed_at'];

    protected function casts(): array
    {
        return ['attendance_date' => 'date', 'opened_at' => 'datetime', 'closed_at' => 'datetime'];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function isOpen(): bool
    {
        return $this->closed_at === null && $this->attendance_date->isToday();
    }

    /** Batas jam terlambat pada tanggal sesi. */
    public function lateCutoff(): Carbon
    {
        return $this->attendance_date->copy()->setTimeFromTimeString($this->late_after);
    }
}
