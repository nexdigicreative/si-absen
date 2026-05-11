<?php
namespace App\Services;

use App\Models\{Attendance, AttendanceDetail, Classes, Student};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Summary for today's dashboard — single aggregate query instead of loading all records.
     */
    public function getTodaySummary(): array
    {
        $today = today();

        $counts = DB::table('attendance_details as ad')
            ->join('attendance as a', 'a.id', '=', 'ad.attendance_id')
            ->where('a.date', $today->toDateString())
            ->selectRaw("
                COUNT(*) as total,
                SUM(ad.status = 'hadir') as hadir,
                SUM(ad.status = 'sakit') as sakit,
                SUM(ad.status = 'izin') as izin,
                SUM(ad.status = 'alfa') as alfa,
                SUM(ad.status = 'terlambat') as terlambat
            ")
            ->first();

        $total = (int) ($counts->total ?? 0);
        $hadir = (int) ($counts->hadir ?? 0);
        $terlambat = (int) ($counts->terlambat ?? 0);

        return [
            'date'       => $today->translatedFormat('l, d F Y'),
            'hadir'      => $hadir,
            'sakit'      => (int) ($counts->sakit ?? 0),
            'izin'       => (int) ($counts->izin ?? 0),
            'alfa'       => (int) ($counts->alfa ?? 0),
            'terlambat'  => $terlambat,
            'total'      => $total,
            'percentage' => $total > 0 ? round(($hadir + $terlambat) / $total * 100, 1) : 0,
        ];
    }

    /**
     * Monthly recap per student for a class — efficient eager loading.
     */
    public function getMonthlyRecap(int $classId, int $month, int $year): Collection
    {
        return Student::active()
            ->forClass($classId)
            ->with([
                'attendanceDetails' => fn($q) =>
                    $q->whereHas(
                        'attendance',
                        fn($a) =>
                        $a->whereMonth('date', $month)->whereYear('date', $year)
                    )
            ])
            ->orderBy('name')
            ->get()
            ->map(fn($student) => [
                'student'   => $student,
                'hadir'     => $student->attendanceDetails->where('status', 'hadir')->count(),
                'sakit'     => $student->attendanceDetails->where('status', 'sakit')->count(),
                'izin'      => $student->attendanceDetails->where('status', 'izin')->count(),
                'alfa'      => $student->attendanceDetails->where('status', 'alfa')->count(),
                'terlambat' => $student->attendanceDetails->where('status', 'terlambat')->count(),
                'total_he'  => (int) config('attendance.working_days', 22),
                'percentage' => $this->calcPercentage($student->attendanceDetails),
            ]);
    }

    /**
     * Monthly trend — single DB query with GROUP BY.
     */
    public function getMonthlyTrend(int $year = null): array
    {
        $year ??= now()->year;

        $rows = DB::table('attendance_details as ad')
            ->join('attendance as a', 'a.id', '=', 'ad.attendance_id')
            ->whereYear('a.date', $year)
            ->selectRaw("
                MONTH(a.date) as m,
                COUNT(*) as total,
                SUM(ad.status IN ('hadir','terlambat')) as present,
                SUM(ad.status = 'alfa') as alfa
            ")
            ->groupByRaw('MONTH(a.date)')
            ->get()
            ->keyBy('m'); // key by month

        return collect(range(1, 12))->map(function ($m) use ($rows) {
            $row = $rows->get($m);
            $total = $row ? (int) $row->total : 0;
            $present = $row ? (int) $row->present : 0;
            $alfa = $row ? (int) $row->alfa : 0;

            return [
                'month'   => $m,
                'label'   => \Carbon\Carbon::create(null, $m)->translatedFormat('M'),
                'percent' => $total > 0 ? round($present / $total * 100, 1) : 0,
                'alfa'    => $alfa,
            ];
        })->values()->all();
    }

    /**
     * Attendance % per class for today — single query with LEFT JOIN.
     */
    public function getClassAttendanceSummary(string|object $date): Collection
    {
        $dateStr = is_string($date) ? $date : $date->toDateString();
        // Get class totals
        $classTotals = Classes::withCount([
            'students as total_students' => fn($q) => $q->active(),
        ])->get()->keyBy('id');

        // Get today's attendance summary per class in one query
        $attendanceSummary = DB::table('attendance as a')
            ->join('attendance_details as ad', 'a.id', '=', 'ad.attendance_id')
            ->where('a.date', $dateStr)
            ->selectRaw("
                a.class_id,
                SUM(ad.status IN ('hadir','terlambat')) as hadir_count
            ")
            ->groupBy('a.class_id')
            ->pluck('hadir_count', 'class_id');

        // Classes that have attendance records today
        $classesWithAttendance = DB::table('attendance')
            ->where('date', $dateStr)
            ->pluck('class_id')
            ->flip();

        return $classTotals->map(function ($class) use ($attendanceSummary, $classesWithAttendance) {
            $hadir = (int) ($attendanceSummary->get($class->id, 0));
            $total = (int) $class->total_students;

            return [
                'class'      => $class,
                'hadir'      => $hadir,
                'total'      => $total,
                'percentage' => $total > 0 ? round($hadir / $total * 100) : 0,
                'done'       => $classesWithAttendance->has($class->id),
            ];
        });
    }

    /**
     * Students with low attendance — DB aggregation with HAVING instead of loading all into memory.
     */
    public function getLowAttendanceStudents(int $threshold = null, int $limit = 10): Collection
    {
        $threshold ??= config('attendance.min_percentage', 75);
        $month = now()->month;
        $year = now()->year;

        $lowStudents = DB::table('attendance_details as ad')
            ->join('attendance as a', 'a.id', '=', 'ad.attendance_id')
            ->join('students as s', 's.id', '=', 'ad.student_id')
            ->whereMonth('a.date', $month)
            ->whereYear('a.date', $year)
            ->where('s.status', true)
            ->whereNull('s.deleted_at')
            ->groupBy('ad.student_id')
            ->selectRaw("
                ad.student_id,
                COUNT(*) as total,
                SUM(ad.status IN ('hadir','terlambat')) as present
            ")
            ->havingRaw("(present / total * 100) < ?", [$threshold])
            ->orderByRaw("(present / total * 100) ASC")
            ->limit($limit)
            ->get();

        if ($lowStudents->isEmpty()) {
            return collect();
        }

        $studentIds = $lowStudents->pluck('student_id');
        $students = Student::with('class')->whereIn('id', $studentIds)->get()->keyBy('id');

        return $lowStudents->map(function ($row) use ($students) {
            $student = $students->get($row->student_id);
            if (!$student) return null;

            return [
                'student'    => $student,
                'percentage' => $row->total > 0 ? round($row->present / $row->total * 100, 1) : 0,
            ];
        })->filter()->values();
    }

    /**
     * Recent system activities — limited query.
     */
    public function getRecentActivities(int $limit = 8): Collection
    {
        return Attendance::with(['class:id,name', 'teacher:id,name'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn($att) => [
                'text' => "Guru {$att->teacher?->name} menginput absensi kelas {$att->class?->name}",
                'time' => $att->created_at->diffForHumans(),
                'type' => 'attendance',
            ]);
    }

    public function getMonthlyChartData(int $year): array
    {
        return $this->getMonthlyTrend($year);
    }

    private function calcPercentage(iterable $details): float
    {
        $details = collect($details);
        $total = $details->count();
        $present = $details->whereIn('status', ['hadir', 'terlambat'])->count();
        return $total > 0 ? round($present / $total * 100, 1) : 0;
    }
}