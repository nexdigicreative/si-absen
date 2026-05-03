{{-- resources/views/classes/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Kelas')
@section('page-title', 'Detail Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0">{{ $class->name }}</h5>
        <span class="text-muted" style="font-size:13px">Tingkat {{ $class->grade }} {{ $class->major ? '· '.$class->major : '' }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('classes.edit', $class) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Info Kelas</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Nama Kelas</dt>
                    <dd class="fw-bold fs-5 mb-2">{{ $class->name }}</dd>
                    <dt class="text-muted small">Tingkat</dt>
                    <dd class="fw-semibold mb-2">Kelas {{ $class->grade }}</dd>
                    <dt class="text-muted small">Jurusan</dt>
                    <dd class="fw-semibold mb-2">{{ $class->major ?? '-' }}</dd>
                    <dt class="text-muted small">Ruang</dt>
                    <dd class="fw-semibold mb-2">{{ $class->room ?? '-' }}</dd>
                    <dt class="text-muted small">Wali Kelas</dt>
                    <dd class="fw-semibold mb-2">{{ $class->homeroomTeacher?->name ?? 'Belum ditentukan' }}</dd>
                    <dt class="text-muted small">Tahun Ajaran</dt>
                    <dd class="fw-semibold mb-2">{{ $class->academicYear?->year ?? '-' }}</dd>
                    <dt class="text-muted small">Kapasitas</dt>
                    <dd class="fw-semibold mb-0">{{ $class->students->count() }} / {{ $class->max_students ?? '-' }} siswa</dd>
                </dl>
            </div>
        </div>

        {{-- Jadwal --}}
        <div class="card mt-4">
            <div class="card-header"><i class="bi bi-calendar3 me-2 text-primary"></i>Jadwal Kelas</div>
            <div class="card-body p-0">
                @php $days = ['','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; @endphp
                @forelse($class->schedules as $sched)
                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                        <span class="badge bg-secondary" style="min-width:56px">{{ $days[$sched->day_of_week] }}</span>
                        <div style="flex:1;font-size:13px">
                            <div class="fw-semibold">{{ $sched->subject }}</div>
                            <div class="text-muted" style="font-size:11px">{{ $sched->teacher?->name }}</div>
                        </div>
                        <span style="font-size:11px;font-family:'JetBrains Mono',monospace">{{ substr($sched->start_time,0,5) }}-{{ substr($sched->end_time,0,5) }}</span>
                    </div>
                @empty
                    <div class="text-center text-muted py-3" style="font-size:13px">Belum ada jadwal.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2 text-primary"></i>Daftar Siswa Aktif</span>
                <span class="badge bg-primary">{{ $class->students->count() }} siswa</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>#</th><th>NIS</th><th>Nama Siswa</th><th>JK</th><th>% Hadir</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($class->students as $i => $student)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><code>{{ $student->nis }}</code></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $student->photo_url }}" width="28" height="28" style="border-radius:50%;object-fit:cover" alt="">
                                        <span class="fw-semibold">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background:{{ $student->gender==='L' ? '#dbeafe' : '#fce7f3' }};color:{{ $student->gender==='L' ? '#1e40af' : '#9d174d' }}">{{ $student->gender }}</span>
                                </td>
                                <td>
                                    @php $pct = $student->attendance_percentage @endphp
                                    <span class="fw-semibold {{ $pct >= 75 ? 'text-success' : 'text-danger' }}">{{ $pct }}%</span>
                                </td>
                                <td>
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
