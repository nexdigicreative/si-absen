<?php
namespace App\Services;

use App\Models\{Attendance, AttendanceDetail, Classes, Student};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /** Summary for today's dashboard */
    public function getTodaySummary(): array
    {
        $today = today();
        $details = AttendanceDetail::whereHas(
            'attendance',
            fn($q) => $q->whereDate('date', $today)
        )->get();

        $total = $details->count();
        $hadir = $details->whereIn('status', ['hadir', 'terlambat'])->count();

        return [
            'date' => $today->translatedFormat('l, d F Y'),
            'hadir' => $details->where('status', 'hadir')->count(),
            'sakit' => $details->where('status', 'sakit')->count(),
            'izin' => $details->where('status', 'izin')->count(),
            'alfa' => $details->where('status', 'alfa')->count(),
            'terlambat' => $details->where('status', 'terlambat')->count(),
            'total' => $total,
            'percentage' => $total > 0 ? round($hadir / $total * 100, 1) : 0,
        ];
    }

    /** Monthly recap per student for a class */
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
                'student' => $student,
                'hadir' => $student->attendanceDetails->where('status', 'hadir')->count(),
                'sakit' => $student->attendanceDetails->where('status', 'sakit')->count(),
                'izin' => $student->attendanceDetails->where('status', 'izin')->count(),
                'alfa' => $student->attendanceDetails->where('status', 'alfa')->count(),
                'terlambat' => $student->attendanceDetails->where('status', 'terlambat')->count(),
                'total_he' => (int) config('attendance.working_days', 22),
                'percentage' => $this->calcPercentage($student->attendanceDetails),
            ]);
    }

    public function getMonthlyTrend(int $year = null): array
    {
        $year ??= now()->year;

        $details = DB::table('attendance_details as ad')
            ->join('attendance as a', 'a.id', '=', 'ad.attendance_id')
            ->whereYear('a.date', $year)
            ->select('a.date', 'ad.status')
            ->get();

        $result = $details->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->date)->month;
        });

        return collect(range(1, 12))->map(function ($m) use ($result) {
            $monthData = $result->get($m, collect());
            $total = $monthData->count();
            $present = $monthData->whereIn('status', ['hadir', 'terlambat'])->count();
            $alfa = $monthData->where('status', 'alfa')->count();

            return [
                'month' => $m,
                'label' => \Carbon\Carbon::create(null, $m)->translatedFormat('M'),
                'percent' => $total > 0 ? round($present / $total * 100, 1) : 0,
                'alfa' => $alfa,
            ];
        })->values()->all();
    }

    /** Attendance % per class for today */
    public function getClassAttendanceSummary(mixed $date): Collection
    {
        $attendances = Attendance::where('date', $date)
            ->withCount([
                'details as hadir_count' => fn($q) => $q->whereIn('status', ['hadir', 'terlambat'])
            ])->get()->keyBy('class_id');

        return Classes::withCount([
            'students as total_students' => fn($q) => $q->active(),
        ])
            ->get()
            ->map(function ($class) use ($attendances) {
                $attendance = $attendances->get($class->id);
                $hadir = $attendance ? $attendance->hadir_count : 0;

                return [
                    'class' => $class,
                    'hadir' => $hadir,
                    'total' => $class->total_students,
                    'percentage' => $class->total_students > 0 ? round($hadir / $class->total_students * 100) : 0,
                    'done' => $attendance !== null,
                ];
            });
    }

    /** Students with attendance % below threshold */
    public function getLowAttendanceStudents(int $threshold = 75, int $limit = 10): Collection
    {
        $month = now()->month;
        $year = now()->year;

        return Student::active()
            ->with(['class', 'attendanceDetails' => function($q) use ($month, $year) {
                $q->whereHas('attendance', fn($a) => $a->whereMonth('date', $month)->whereYear('date', $year));
            }])
            ->get()
            ->map(fn($s) => [
                'student' => $s,
                'percentage' => $this->calcPercentage($s->attendanceDetails),
            ])
            ->filter(fn($row) => $row['percentage'] < $threshold)
            ->sortBy('percentage')
            ->take($limit)
            ->values();
    }

    /** Recent system activities */
    public function getRecentActivities(int $limit = 8): Collection
    {
        return Attendance::with(['class', 'teacher'])
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