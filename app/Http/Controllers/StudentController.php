<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Imports\StudentsImport;
use App\Models\{Classes, Student, User, AcademicYear};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Crypt, Storage};
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['class.academicYear'])
            ->withCount(['attendanceDetails as attendance_details_count'])
            ->withCount(['attendanceDetails as present_count' => fn($q) => $q->whereIn('status', ['hadir', 'terlambat'])])
            ->when(
                $request->search,
                fn($q) =>
                $q->where(function($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%")
                        ->orWhere('nis', 'like', "%{$request->search}%")
                        ->orWhere('nisn', 'like', "%{$request->search}%");
                })
            )
            ->when($request->class_id, fn($q) => $q->where('class_id', $request->class_id))
            ->when($request->gender, fn($q) => $q->where('gender', $request->gender))
            ->when($request->status !== null, fn($q) => $q->where('status', $request->status))
            ->orderBy('name');

        $students = $query->paginate(20)->withQueryString();
        $classes = Classes::orderBy('name')->get();
        $academicYear = AcademicYear::active();

        return view('students.index', compact('students', 'classes', 'academicYear'));
    }

    public function create()
    {
        $classes = Classes::with('academicYear')->orderBy('name')->get();
        return view('students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('students/photos', 'public');
        }

        // Create linked user account if requested with random password
        if ($request->boolean('create_account')) {
            $defaultPassword = \Illuminate\Support\Str::random(8);
            $user = User::create([
                'username' => $data['nis'],
                'password' => bcrypt($defaultPassword),
                'name' => $data['name'],
                'email' => $data['parent_email'] ?? null,
                'role' => 'siswa',
            ]);
            $data['user_id'] = $user->id;
            session()->flash('temp_password', $defaultPassword);
        }

        Student::create($data);

        $msg = "Siswa {$data['name']} berhasil ditambahkan.";
        if (session('temp_password')) {
            $msg .= " Akun dibuat dengan Username: {$data['nis']} dan Password: " . session('temp_password');
        }

        return redirect()->route('students.index')->with('success', $msg);
    }

    public function show(Student $student)
    {
        $student->load(['class.homeroomTeacher', 'attendanceDetails.attendance']);

        $monthlyStats = $student->attendanceDetails()
            ->selectRaw('status, COUNT(*) as total')
            ->whereHas(
                'attendance',
                fn($q) =>
                $q->whereMonth('date', now()->month)->whereYear('date', now()->year)
            )
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('students.show', compact('student', 'monthlyStats'));
    }

    public function edit(Student $student)
    {
        $classes = Classes::with('academicYear')->orderBy('name')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(StoreStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student->update($data);

        return redirect()->route('students.index')
            ->with('success', "Data siswa {$student->name} berhasil diperbarui.");
    }

    public function destroy(Student $student)
    {
        $name = $student->name;
        $student->delete(); // Soft delete
        return redirect()->route('students.index')
            ->with('success', "Siswa {$name} berhasil dihapus.");
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:2048']);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return redirect()->route('students.index')
                ->with('success', 'Import data siswa berhasil!');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Import gagal: ' . $e->getMessage()]);
        }
    }

    public function attendanceHistory(Student $student, Request $request)
    {
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

        return view('students.attendance-history', compact('student', 'history', 'month', 'year'));
    }

    public function printCard(Student $student)
    {
        $student->load('class');
        $payload = json_encode(['id' => $student->id, 'nis' => $student->nis]);
        $qrString = Crypt::encryptString($payload);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
            ->margin(1)
            ->style('round')
            ->generate($qrString);

        return view('students.print-card', compact('student', 'qrSvg'));
    }

    public function myCard()
    {
        $student = auth()->user()->student;
        if ($student) {
            $student->load('class');
        }
        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $payload = json_encode(['id' => $student->id, 'nis' => $student->nis]);
        $qrString = Crypt::encryptString($payload);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)
            ->margin(1)
            ->style('round')
            ->generate($qrString);

        return view('students.print-card', compact('student', 'qrSvg'));
    }
}
