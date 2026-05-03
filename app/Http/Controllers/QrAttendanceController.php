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
        $payload = $request->input('qr_code'); // the encrypted payload
        
        try {
            try {
                // Try decrypting new secure JSON format
                $data = json_decode(Crypt::decryptString($payload), true);
                $studentId = $data['id'] ?? null;
                $nis = $data['nis'] ?? null;
                $student = $studentId ? Student::find($studentId) : null;
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Fallback to legacy format: "SIABSEN-{nis}"
                if (str_starts_with($payload, 'SIABSEN-')) {
                    $nis = str_replace('SIABSEN-', '', $payload);
                    $student = Student::where('nis', $nis)->first();
                } else {
                    return response()->json(['success' => false, 'message' => 'QR Code tidak dikenali.']);
                }
            }

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Format QR tidak valid / Siswa tidak ditemukan.']);
            }

            // Get or create today's attendance for the student's class
            $attendance = Attendance::firstOrCreate(
                [
                    'date' => today()->toDateString(),
                    'class_id' => $student->class_id,
                    'session' => 'pagi'
                ],
                ['teacher_id' => 1] // System/Admin default
            );

            // Check if already checked in
            $existing = AttendanceDetail::where('attendance_id', $attendance->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false, 
                    'message' => "{$student->name} sudah absen hari ini."
                ]);
            }

            // Determine status based on time (late limit)
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
                'message' => "Absen berhasil: {$student->name} (" . strtoupper($status) . ")"
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
        }
    }

    /**
     * Halaman Generate QR Kelas (untuk Guru)
     */
    public function generate(Request $request)
    {
        $classes = Classes::orderBy('name')->get();
        $classId = $request->get('class_id');
        $date = today()->toDateString();
        
        $qrString = null;
        if ($classId) {
            // The QR code contains the class ID and date, encrypted to prevent spoofing
            $payload = json_encode(['class_id' => $classId, 'date' => $date]);
            $qrString = Crypt::encryptString($payload);
        }

        return view('attendance.qr.generate', compact('classes', 'classId', 'qrString', 'date'));
    }

    /**
     * Halaman Siswa Untuk Scan QR Kelas
     */
    public function scanPage()
    {
        return view('attendance.qr.scan-page');
    }

    /**
     * Proses Siswa Meng-scan QR Kelas
     */
    public function processScan(Request $request)
    {
        $payload = $request->input('qr_code');
        $student = auth()->user()->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Bukan akun siswa.']);
        }

        try {
            try {
                $data = json_decode(Crypt::decryptString($payload), true);
                $classId = $data['class_id'] ?? null;
                $date = $data['date'] ?? null;
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kedaluwarsa.']);
            }

            if ($classId != $student->class_id) {
                return response()->json(['success' => false, 'message' => 'Ini bukan QR Code kelas Anda.']);
            }

            if ($date != today()->toDateString()) {
                return response()->json(['success' => false, 'message' => 'QR Code sudah kedaluwarsa (berbeda hari).']);
            }

            $attendance = Attendance::firstOrCreate(
                [
                    'date' => $date,
                    'class_id' => $classId,
                    'session' => 'pagi'
                ],
                ['teacher_id' => 1]
            );

            $existing = AttendanceDetail::where('attendance_id', $attendance->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Anda sudah merekam absen hari ini.']);
            }

            $lateLimit = config('attendance.late_limit', '07:15:00');
            $status = now()->format('H:i:s') > $lateLimit ? 'terlambat' : 'hadir';

            AttendanceDetail::create([
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'status' => $status,
                'check_in' => now()->format('H:i:s')
            ]);

            return response()->json(['success' => true, 'message' => "Absen kelas berhasil dicatat (" . strtoupper($status) . ")."]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak dikenali atau telah dimodifikasi.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal.']);
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
            ->orderBy('created_at', 'desc')
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
