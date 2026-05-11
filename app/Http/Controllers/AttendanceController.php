<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Jobs\SendAbsenceNotification;
use App\Models\{Attendance, AttendanceDetail, Classes, Student};
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $service)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'siswa' && $user->student) {
            $classId = $user->student->class_id;
            $classes = collect([$user->student->class]);
        } else {
            $classId = $request->class_id;
            $classes = Classes::orderBy('name')->get();
        }

        $query = Attendance::with(['class', 'teacher', 'details'])
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->when($request->date, fn($q) => $q->whereDate('date', $request->date))
            ->when($request->month, fn($q) => $q->whereMonth('date', $request->month))
            ->orderByDesc('date');

        $attendances = $query->paginate(20)->withQueryString();

        return view('attendance.index', compact('attendances', 'classes'));
    }

    public function create(Request $request)
    {
        $classes = Classes::with('students')->orderBy('name')->get();
        $date = $request->get('date', today()->toDateString());
        $classId = $request->get('class_id');

        $students = $classId
            ? Student::forClass($classId)->active()->orderBy('name')->get()
            : collect();

        // Load existing attendance for this session
        $existing = [];
        if ($classId && $date) {
            $att = Attendance::where(['date' => $date, 'class_id' => $classId])->first();
            if ($att) {
                $existing = $att->details()->pluck('status', 'student_id')->toArray();
            }
        }

        return view('attendance.create', compact('classes', 'students', 'date', 'classId', 'existing'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        DB::transaction(function () use ($request) {
            $attendance = Attendance::updateOrCreate(
                [
                    'date' => $request->date,
                    'class_id' => $request->class_id,
                    'session' => $request->get('session', 'pagi'),
                ],
                ['teacher_id' => Auth::user()->teacher?->id ?? 1]
            );

            $detailsData = [];
            foreach ($request->details as $studentId => $detail) {
                $detailsData[] = [
                    'attendance_id' => $attendance->id,
                    'student_id'    => (int) $studentId,
                    'status'        => $detail['status'],
                    'check_in'      => $detail['check_in'] ?? null,
                    'notes'         => $detail['notes'] ?? null,
                ];
            }

            if (!empty($detailsData)) {
                AttendanceDetail::upsert($detailsData, ['attendance_id', 'student_id'], ['status', 'check_in', 'notes']);
            }

            // Notify parents of alfa students (queued)
            $alfaIds = collect($request->details)
                ->filter(fn($d) => $d['status'] === 'alfa')
                ->keys();

            foreach ($alfaIds as $studentId) {
                SendAbsenceNotification::dispatch($studentId, $request->date)
                    ->onQueue('notifications')
                    ->delay(now()->addSeconds(10));
            }
        });

        return redirect()->route('attendance.index')
            ->with('success', 'Absensi berhasil disimpan!');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['class', 'teacher', 'details.student']);
        return view('attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load(['details.student', 'class.students']);
        $existingDetails = $attendance->details->keyBy('student_id');
        return view('attendance.edit', compact('attendance', 'existingDetails'));
    }

    public function update(StoreAttendanceRequest $request, Attendance $attendance)
    {
        DB::transaction(function () use ($request, $attendance) {
            $detailsData = [];
            foreach ($request->details as $studentId => $detail) {
                $detailsData[] = [
                    'attendance_id' => $attendance->id,
                    'student_id'    => (int) $studentId,
                    'status'        => $detail['status'],
                    'check_in'      => $detail['check_in'] ?? null,
                    'notes'         => $detail['notes'] ?? null,
                ];
            }

            if (!empty($detailsData)) {
                AttendanceDetail::upsert($detailsData, ['attendance_id', 'student_id'], ['status', 'check_in', 'notes']);
            }
        });

        return redirect()->route('attendance.show', $attendance)
            ->with('success', 'Absensi berhasil diperbarui!');
    }

    public function myAttendance(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan. Hubungi administrator.');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $history = $student->attendanceDetails()
            ->with('attendance')
            ->whereHas(
                'attendance',
                fn($q) =>
                $q->whereMonth('date', $month)->whereYear('date', $year)
            )
            ->orderByDesc('id')
            ->get();

        $stats = $history->groupBy('status')
            ->map->count();

        return view('attendance.my-attendance', compact('student', 'history', 'stats', 'month', 'year'));
    }
}