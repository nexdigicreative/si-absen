{{-- resources/views/attendance/my-attendance.blade.php --}}
@extends('layouts.app')
@section('title', 'Absensi Saya')
@section('page-title', 'Absensi Saya')

@section('content')
    {{-- Header info siswa --}}
    <div class="card mb-4" style="background:linear-gradient(135deg,#1a56db,#8b5cf6);border:none">
        <div class="card-body p-4 d-flex align-items-center gap-4">
            <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.2);display:grid;place-items:center;font-size:28px;font-weight:800;color:#fff;flex-shrink:0">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <div>
                <div class="text-white fw-bold" style="font-size:20px">{{ $student->name }}</div>
                <div style="color:rgba(255,255,255,.8);font-size:13px">{{ $student->nis }} · {{ $student->class?->name }}</div>
                <div style="color:rgba(255,255,255,.6);font-size:12px">Wali Kelas: {{ $student->class?->homeroomTeacher?->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Filter bulan --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                    <select name="month" class="form-select">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                    <select name="year" class="form-select">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-search me-1"></i>Lihat</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        @php
            $total = $history->count();
            $pct = $total > 0 ? round(($stats->get('hadir',0) + $stats->get('terlambat',0)) / $total * 100) : 0;
        @endphp
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#d1fae5;color:#10b981"><i class="bi bi-check-circle-fill"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats->get('hadir',0) }}</div>
                <div class="text-muted" style="font-size:12px">Hadir</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fef3c7;color:#f59e0b"><i class="bi bi-clock-fill"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats->get('terlambat',0) }}</div>
                <div class="text-muted" style="font-size:12px">Terlambat</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fee2e2;color:#ef4444"><i class="bi bi-x-circle-fill"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats->get('alfa',0) }}</div>
                <div class="text-muted" style="font-size:12px">Alfa</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#dbeafe;color:#1a56db"><i class="bi bi-percent"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace;color:{{ $pct >= 75 ? '#10b981' : '#ef4444' }}">{{ $pct }}%</div>
                <div class="text-muted" style="font-size:12px">Kehadiran</div>
            </div>
        </div>
    </div>

    @if($pct < 75)
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>Persentase kehadiran Anda <strong>{{ $pct }}%</strong> — di bawah batas minimal <strong>75%</strong>. Segera hubungi wali kelas.</div>
        </div>
    @endif

    {{-- Tabel riwayat --}}
    <div class="card">
        <div class="card-header">
            <i class="bi bi-calendar3 me-2 text-primary"></i>
            Riwayat Bulan {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Sesi</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $i => $detail)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:13px">
                                {{ $detail->attendance->date->format('d/m/Y') }}
                            </td>
                            <td>{{ $detail->attendance->date->translatedFormat('l') }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($detail->attendance->session) }}</span></td>
                            <td>
                                <span class="badge badge-status-{{ $detail->status }}">
                                    @switch($detail->status)
                                        @case('hadir') ✅ Hadir @break
                                        @case('terlambat') ⏰ Terlambat @break
                                        @case('sakit') 🤒 Sakit @break
                                        @case('izin') 📄 Izin @break
                                        @case('alfa') ❌ Alfa @break
                                    @endswitch
                                </span>
                            </td>
                            <td style="font-size:13px">{{ $detail->check_in ?? '-' }}</td>
                            <td style="font-size:12px;color:#64748b">{{ $detail->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Tidak ada riwayat absensi pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
