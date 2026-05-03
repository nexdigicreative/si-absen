{{-- resources/views/teachers/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <p class="text-muted mb-0">Total: <strong>{{ $teachers->total() }}</strong> guru</p>
    <a href="{{ route('teachers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Guru
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama, NIP, mata pelajaran..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NIP</th>
                    <th>Nama Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Wali Kelas</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $i => $teacher)
                    <tr>
                        <td>{{ $teachers->firstItem() + $i }}</td>
                        <td><code>{{ $teacher->nip ?? '-' }}</code></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width:32px;height:32px;font-size:12px;background:linear-gradient(135deg,#1a56db,#8b5cf6)">
                                    {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $teacher->name }}</div>
                                    <div class="text-muted" style="font-size:11px">{{ $teacher->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge" style="background:#ede9fe;color:#5b21b6">{{ $teacher->subject ?? '-' }}</span></td>
                        <td style="font-size:13px">
                            @forelse($teacher->homeroomClasses as $cls)
                                <span class="badge" style="background:#e8f0fe;color:#1a56db">{{ $cls->name }}</span>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </td>
                        <td style="font-size:13px">{{ $teacher->phone ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $teacher->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $teacher->status ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form id="del-t{{ $teacher->id }}" method="POST" action="{{ route('teachers.destroy', $teacher) }}">@csrf @method('DELETE')</form>
                                <button class="btn btn-sm btn-outline-danger" data-confirm="Hapus guru {{ $teacher->name }}?" data-form="del-t{{ $teacher->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-person-badge fs-1 d-block mb-2"></i>
                            Belum ada data guru.
                            <a href="{{ route('teachers.create') }}" class="btn btn-sm btn-primary mt-2">+ Tambah Guru</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted" style="font-size:13px">Menampilkan {{ $teachers->firstItem() }}-{{ $teachers->lastItem() }} dari {{ $teachers->total() }}</div>
        {{ $teachers->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
