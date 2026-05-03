{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('page-title', 'Laporan Absensi')

@section('content')
    {{-- Ringkasan Sekolah --}}
    @if($summary)
        <div class="row g-3 mb-4">
            @foreach([
                ['label'=>'Total Siswa','value'=>$summary['total_students'],'icon'=>'bi-people-fill','bg'=>'#e8f0fe','color'=>'#1a56db'],
                ['label'=>'Avg. Kehadiran','value'=>($summary['avg_attendance'] ?? 0).'%','icon'=>'bi-graph-up','bg'=>'#d1fae5','color'=>'#10b981'],
                ['label'=>'Hadir Bulan Ini','value'=>$summary['total_hadir'] ?? 0,'icon'=>'bi-check-circle-fill','bg'=>'#d1fae5','color'=>'#10b981'],
                ['label'=>'Alfa Bulan Ini','value'=>$summary['total_alfa'] ?? 0,'icon'=>'bi-x-circle-fill','bg'=>'#fee2e2','color'=>'#ef4444'],
            ] as $s)
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:{{ $s['bg'] }};color:{{ $s['color'] }}"><i class="bi {{ $s['icon'] }}"></i></div>
                        <div style="font-size:26px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $s['value'] }}</div>
                        <div style="color:#64748b;font-size:12px">{{ $s['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Navigasi Laporan --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="{{ route('reports.daily') }}" class="card text-decoration-none h-100" style="transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                <div class="card-body text-center p-4">
                    <div style="width:56px;height:56px;border-radius:12px;background:#e8f0fe;display:grid;place-items:center;font-size:24px;margin:0 auto 12px;color:#1a56db">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Laporan Harian</h5>
                    <p class="text-muted mb-0" style="font-size:13px">Lihat rincian absensi per tanggal</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('reports.monthly') }}" class="card text-decoration-none h-100" style="transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                <div class="card-body text-center p-4">
                    <div style="width:56px;height:56px;border-radius:12px;background:#d1fae5;display:grid;place-items:center;font-size:24px;margin:0 auto 12px;color:#10b981">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Laporan Bulanan</h5>
                    <p class="text-muted mb-0" style="font-size:13px">Trend absensi per bulan</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('reports.recap') }}" class="card text-decoration-none h-100" style="transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                <div class="card-body text-center p-4">
                    <div style="width:56px;height:56px;border-radius:12px;background:#ede9fe;display:grid;place-items:center;font-size:24px;margin:0 auto 12px;color:#8b5cf6">
                        <i class="bi bi-table"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Rekap Bulanan</h5>
                    <p class="text-muted mb-0" style="font-size:13px">Rekap per siswa tiap bulan</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Export --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-download me-2 text-primary"></i>Export Laporan</div>
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                    <select name="month" class="form-select">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected':'' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                    <select name="year" class="form-select">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected':'' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button formaction="{{ route('reports.pdf') }}" type="submit" class="btn btn-outline-danger w-100 mt-3">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                    </button>
                </div>
                <div class="col-md-2">
                    <button formaction="{{ route('reports.excel') }}" type="submit" class="btn btn-outline-success w-100 mt-3">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
