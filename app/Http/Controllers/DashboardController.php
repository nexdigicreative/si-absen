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
        $user = auth()->user();
        $today = today();
        $dayOfWeek = (string) now()->format('N'); // 1 = Mon, 7 = Sun

        // ── Common stats for all roles ──
        $stats = $this->attendanceService->getTodaySummary();

        // ── Role-specific data ──
        $data = [
            'stats'         => $stats,
            'monthly_trend' => $this->attendanceService->getMonthlyTrend(),
        ];

        if ($user->isSiswa()) {
            $student = $user->student;
            $data['today_schedules'] = $student?->class_id
                ? Schedule::with('teacher:id,name,photo')
                    ->where('class_id', $student->class_id)
                    ->where('day_of_week', $dayOfWeek)
                    ->orderBy('start_time')->get()
                : collect();

            // Siswa doesn't need admin-level stats
            $data['total_students'] = 0;
            $data['total_teachers'] = 0;
            $data['total_classes']  = 0;
            $data['class_attendance'] = collect();
            $data['low_attendance']   = collect();
            $data['recent_activities'] = collect();

        } elseif ($user->isGuru()) {
            $teacher = $user->teacher;
            $data['today_schedules'] = $teacher
                ? Schedule::with('class:id,name')
                    ->where('teacher_id', $teacher->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->orderBy('start_time')->get()
                : collect();

            // Guru sees summary of classes they teach
            $data['total_students']    = Student::active()->count();
            $data['total_teachers']    = Teacher::active()->count();
            $data['total_classes']     = Classes::count();
            $data['class_attendance']  = $this->attendanceService->getClassAttendanceSummary($today);
            $data['low_attendance']    = collect(); // Only admin/kepsek see this
            $data['recent_activities'] = $this->attendanceService->getRecentActivities(5);

        } else {
            // Admin & Kepala Sekolah — full overview
            $data['today_schedules']   = collect();
            $data['total_students']    = Student::active()->count();
            $data['total_teachers']    = Teacher::active()->count();
            $data['total_classes']     = Classes::count();
            $data['class_attendance']  = $this->attendanceService->getClassAttendanceSummary($today);
            $data['low_attendance']    = $this->attendanceService->getLowAttendanceStudents();
            $data['recent_activities'] = $this->attendanceService->getRecentActivities();
        }

        return view('dashboard.index', $data);
    }

    public function chartData(Request $request)
    {
        $year = $request->get('year', now()->year);
        $data = $this->attendanceService->getMonthlyChartData($year);
        return response()->json($data);
    }
}