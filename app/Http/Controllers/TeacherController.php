<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StoreTeacherRequest, UpdateTeacherRequest};
use App\Models\{Classes, Teacher, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::with(['user', 'homeroomClasses'])
            ->when(
                $request->search,
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('nip', 'like', "%{$request->search}%")
                    ->orWhere('subject', 'like', "%{$request->search}%")
            )
            ->when($request->status !== null, fn($q) => $q->where('status', $request->status))
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers/photos', 'public');
        }

        // Auto-create user account with random password
        $username = $data['nip'] ?? strtolower(str_replace(' ', '.', $data['name']));
        $defaultPassword = \Illuminate\Support\Str::random(8);
        $user = User::create([
            'username' => $username,
            'password' => bcrypt($defaultPassword),
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'role' => 'guru',
        ]);

        $data['user_id'] = $user->id;
        Teacher::create($data);

        return redirect()->route('teachers.index')
            ->with('success', "Guru {$data['name']} berhasil ditambahkan. Username: {$username}, Password: {$defaultPassword}");
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['homeroomClasses', 'schedules.class', 'attendances' => fn($q) => $q->latest()->limit(10)]);
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($teacher->photo)
                Storage::disk('public')->delete($teacher->photo);
            $data['photo'] = $request->file('photo')->store('teachers/photos', 'public');
        }

        $teacher->update($data);
        $teacher->user?->update(['name' => $data['name'], 'email' => $data['email']]);

        return redirect()->route('teachers.index')
            ->with('success', "Data guru {$teacher->name} berhasil diperbarui.");
    }

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->name;
        $teacher->user?->delete();
        $teacher->delete();
        return redirect()->route('teachers.index')
            ->with('success', "Guru {$name} berhasil dihapus.");
    }
}