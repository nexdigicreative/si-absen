{{-- resources/views/attendance/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Daftar Absensi')
@section('page-title', 'Daftar Absensi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <p class="text-muted mb-0">Semua sesi absensi yang tercatat</p>
        <div class="d-flex gap-2">
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Input Absensi
            </a>
            <a href="{{ route('attendance.qr.generate') }}" class="btn btn-outline-secondary">
                <i class="bi bi-qr-code me-1"></i>Generate QR
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-search me-1"></i>Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary w-100 mt-3">Reset</a>
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
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Sesi</th>
                        <th>Guru</th>
                        <th>Hadir</th>
                        <th>Alfa</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $att)
                        <tr>
                            <td>{{ $attendances->firstItem() + $i }}</td>
                            <td>
                                <div class="fw-semibold">{{ $att->date->translatedFormat('d M Y') }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $att->date->translatedFormat('l') }}</div>
                            </td>
                            <td><span class="badge" style="background:#e8f0fe;color:#1a56db">{{ $att->class?->name }}</span></td>
                            <td>
                                <span class="badge {{ $att->session === 'pagi' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                                    {{ ucfirst($att->session) }}
                                </span>
                            </td>
                            <td style="font-size:13px">{{ $att->teacher?->name ?? '-' }}</td>
                            <td><span class="badge badge-status-hadir">{{ $att->details_count_hadir ?? $att->details->where('status','hadir')->count() }}</span></td>
                            <td><span class="badge badge-status-alfa">{{ $att->details->where('status','alfa')->count() }}</span></td>
                            <td><span class="badge badge-status-sakit">{{ $att->details->where('status','sakit')->count() }}</span></td>
                            <td><span class="badge badge-status-izin">{{ $att->details->where('status','izin')->count() }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('attendance.show', $att) }}" class="btn btn-sm btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('attendance.edit', $att) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                Belum ada data absensi.
                                <a href="{{ route('attendance.create') }}" class="btn btn-sm btn-primary mt-2">+ Input Absensi</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted" style="font-size:13px">
                @if($attendances->total() > 0)
                    Menampilkan {{ $attendances->firstItem() }}-{{ $attendances->lastItem() }} dari {{ $attendances->total() }}
                @else
                    Tidak ada data
                @endif
            </div>
            {{ $attendances->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
