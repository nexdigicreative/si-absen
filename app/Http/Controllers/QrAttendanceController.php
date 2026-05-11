<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Classes;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class QrAttendanceController extends Controller
{
    /**
     * Halaman Scanner Sekolah (Standalone Scanner) 
     * Biasanya di Front Desk/Lobi, discan oleh satpam/guru piket untuk siswa yang datang.
     */
    public function scanner()
    {
        return view('attendance.qr.scanner');
    }

    /**
     * Proses Scan Kartu Siswa (dari Front Desk)
     */
    public function processCardScan(Request $request)
    {
        $payload = $request->input('qr_code');
        
        try {
            try {
                $data = json_decode(Crypt::decryptString($payload), true);
                $studentId = $data['id'] ?? null;
                $student = $studentId ? Student::find($studentId) : null;
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                if (str_starts_with($payload, 'SIABSEN-')) {
                    $nis = str_replace('SIABSEN-', '', $payload);
                    $student = Student::where('nis', $nis)->first();
                } else {
                    return response()->json(['success' => false, 'message' => 'QR Code tidak dikenali.']);
                }
            }

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
            }

            // Get homeroom teacher as default teacher for this attendance record
            $teacherId = $student->class?->teacher_id ?? 1;

            $attendance = Attendance::firstOrCreate(
                [
                    'date' => today()->toDateString(),
                    'class_id' => $student->class_id,
                    'session' => 'pagi'
                ],
                ['teacher_id' => $teacherId]
            );

            $existing = AttendanceDetail::where('attendance_id', $attendance->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false, 
                    'message' => "{$student->name} sudah tercatat hadir."
                ]);
            }

            $lateLimit = config('attendance.late_limit', '07:15:00');
            $status = now()->format('H:i:s') > $lateLimit ? 'terlambat' : 'hadir';

            AttendanceDetail::create([
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'status' => $status,
                'check_in' => now()->format('H:i:s')
            ]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil: {$student->name} (" . strtoupper($status) . ")"
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Halaman Generate QR Kelas (untuk Guru)
     */
    public function generate(Request $request)
    {
        $user = auth()->user();
        
        // If teacher, prioritize their classes
        if ($user->isGuru()) {
            $classes = Classes::where('teacher_id', $user->teacher?->id)->orderBy('name')->get();
            if ($classes->isEmpty()) {
                $classes = Classes::orderBy('name')->get();
            }
        } else {
            $classes = Classes::orderBy('name')->get();
        }

        $classId = $request->get('class_id');
        $date = today()->toDateString();
        
        $qrString = null;
        if ($classId) {
            $payload = json_encode([
                'class_id' => (int)$classId, 
                'date' => $date,
                'teacher_id' => $user->teacher?->id ?? 1,
                'ts' => time() // entropy
            ]);
            $qrString = Crypt::encryptString($payload);
        }

        return view('attendance.qr.generate', compact('classes', 'classId', 'qrString', 'date'));
    }

    /**
     * Halaman Siswa Untuk Scan QR Kelas
     */
    public function scanPage()
    {
        if (!auth()->user()->isSiswa()) {
            return redirect()->route('dashboard')->with('error', 'Hanya siswa yang dapat mengakses halaman scan.');
        }
        return view('attendance.qr.scan-page');
    }

    /**
     * Proses Siswa Meng-scan QR Kelas
     */
    public function processScan(Request $request)
    {
        $payload = $request->input('qr_code');
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Akun Anda tidak tertaut dengan data siswa.']);
        }

        try {
            $data = json_decode(Crypt::decryptString($payload), true);
            $classId = $data['class_id'] ?? null;
            $date = $data['date'] ?? null;
            $teacherId = $data['teacher_id'] ?? 1;
            
            // Validate QR age (optional security: e.g., max 5 minutes old if real-time)
            // if (time() - ($data['ts'] ?? 0) > 300) ...

            if ($classId != $student->class_id) {
                return response()->json(['success' => false, 'message' => 'Ini bukan QR Code untuk kelas Anda.']);
            }

            if ($date != today()->toDateString()) {
                return response()->json(['success' => false, 'message' => 'QR Code ini sudah kedaluwarsa.']);
            }

            $attendance = Attendance::firstOrCreate(
                [
                    'date' => $date,
                    'class_id' => $classId,
                    'session' => 'pagi'
                ],
                ['teacher_id' => $teacherId]
            );

            $existing = AttendanceDetail::where('attendance_id', $attendance->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen hari ini.']);
            }

            $lateLimit = config('attendance.late_limit', '07:15:00');
            $status = now()->format('H:i:s') > $lateLimit ? 'terlambat' : 'hadir';

            AttendanceDetail::create([
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'status' => $status,
                'check_in' => now()->format('H:i:s')
            ]);

            return response()->json(['success' => true, 'message' => "Berhasil! Kehadiran dicatat sebagai " . strtoupper($status)]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kedaluwarsa.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memproses absen: ' . $e->getMessage()]);
        }
    }

    /**
     * Live Monitoring dari Dashboard Guru
     */
    public function monitor(Request $request)
    {
        $classId = $request->get('class_id');
        return view('attendance.qr.monitor', compact('classId'));
    }

    public function liveData(Request $request)
    {
        $classId = $request->get('class_id');
        if (!$classId) {
            return response()->json([]);
        }

        $attendance = Attendance::where('date', today()->toDateString())
            ->where('class_id', $classId)
            ->first();

        if (!$attendance) {
            return response()->json([]);
        }

        $details = AttendanceDetail::with('student:id,name,nis')
            ->where('attendance_id', $attendance->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($d) {
                return [
                    'name' => $d->student->name,
                    'nis' => $d->student->nis,
                    'time' => $d->check_in,
                    'status' => $d->status,
                ];
            });

        return response()->json($details);
    }
}
