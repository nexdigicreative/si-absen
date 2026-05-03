<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StoreClassRequest, UpdateClassRequest};
use App\Models\{AcademicYear, Classes, Teacher};
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::with(['homeroomTeacher', 'academicYear'])
            ->withCount('students')
            ->orderBy('grade')->orderBy('name')
            ->get();

        $activeYear = AcademicYear::active();
        return view('classes.index', compact('classes', 'activeYear'));
    }

    public function create()
    {
        $teachers = Teacher::active()->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('is_active')->orderByDesc('year')->get();
        return view('classes.create', compact('teachers', 'academicYears'));
    }

    public function store(StoreClassRequest $request)
    {
        $data = $request->validated();

        Classes::create($data);

        return redirect()->route('classes.index')
            ->with('success', "Kelas {$data['name']} berhasil ditambahkan.");
    }

    public function show(Classes $class)
    {
        $class->load(['homeroomTeacher', 'students' => fn($q) => $q->active()->orderBy('name'), 'schedules.teacher']);
        return view('classes.show', compact('class'));
    }

    public function edit(Classes $class)
    {
        $teachers = Teacher::active()->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('year')->get();
        return view('classes.edit', compact('class', 'teachers', 'academicYears'));
    }

    public function update(UpdateClassRequest $request, Classes $class)
    {
        $data = $request->validated();

        $class->update($data);
        return redirect()->route('classes.index')
            ->with('success', "Kelas {$class->name} berhasil diperbarui.");
    }

    public function destroy(Classes $class)
    {
        if ($class->students()->count() > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus kelas yang masih memiliki siswa.']);
        }
        $name = $class->name;
        $class->delete();
        return redirect()->route('classes.index')
            ->with('success', "Kelas {$name} berhasil dihapus.");
    }
}