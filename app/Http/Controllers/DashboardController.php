<?php

namespace App\Http\Controllers;

use App\Models\{Attendance, AttendanceDetail, Classes, Schedule, Student, Teacher};
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService)
    {
    }

    public function index()
    {
        $today = today();
        $stats = $this->attendanceService->getTodaySummary();
        $user = auth()->user();
        $todayStr = (string) now()->format('N'); // 1 = Sen, 7 = Ming
        
        $todaySchedules = collect();
        if ($user->role === 'siswa' && $user->student?->class_id) {
            $todaySchedules = Schedule::with('teacher')
                ->where('class_id', $user->student->class_id)
                ->where('day_of_week', $todayStr)
                ->orderBy('start_time')->get();
        } elseif ($user->role === 'guru' && $user->teacher) {
            $todaySchedules = Schedule::with('class')
                ->where('teacher_id', $user->teacher->id)
                ->where('day_of_week', $todayStr)
                ->orderBy('start_time')->get();
        }

        $monthly = $this->attendanceService->getMonthlyTrend();

        $data = [
            'stats' => $stats,
            'today_schedules' => $todaySchedules,
            'monthly_trend' => $monthly,
            'total_students' => Student::active()->count(),
            'total_teachers' => Teacher::active()->count(),
            'total_classes' => Classes::count(),
            'class_attendance' => $this->attendanceService->getClassAttendanceSummary($today),
            'low_attendance' => $this->attendanceService->getLowAttendanceStudents(),
            'recent_activities' => $this->attendanceService->getRecentActivities(),
        ];

        return view('dashboard.index', $data);
    }

    public function chartData(Request $request)
    {
        $year = $request->get('year', now()->year);
        $data = $this->attendanceService->getMonthlyChartData($year);
        return response()->json($data);
    }
}