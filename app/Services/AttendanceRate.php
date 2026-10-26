<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\CarbonInterface;

/**
 * Rate absensi: mulai 100% dan turun sesuai seberapa sering siswa tidak masuk.
 *  - dihitung: hadir, terlambat, sakit, izin, alfa
 *  - tidak dihitung sama sekali: dispensasi
 *  - dianggap masuk: hadir + terlambat  (ubah di sini jika "terlambat" harus mengurangi rate)
 */
class AttendanceRate
{
    public const ATTENDED = ['present', 'late'];
    public const COUNTED = ['present', 'late', 'sick', 'permission', 'absent'];

    public static function summary(User $student, ?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        $query = Attendance::where('student_id', $student->id);
        if ($from) {
            $query->whereDate('attendance_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('attendance_date', '<=', $to);
        }
        $counts = $query->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $get = fn (string $s) => (int) ($counts[$s] ?? 0);
        $counted = array_sum(array_map($get, self::COUNTED));
        $attended = array_sum(array_map($get, self::ATTENDED));

        return [
            'rate' => $counted > 0 ? round($attended / $counted * 100, 1) : 100.0,
            'days' => $counted,
            'present' => $get('present'),
            'late' => $get('late'),
            'sick' => $get('sick'),
            'permission' => $get('permission'),
            'absent' => $get('absent'),
            'dispensation' => $get('dispensation'),
        ];
    }
}
