{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- ── HERO SECTION ── --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius:1.5rem; background: #0f172a;">
        <div class="row g-0 align-items-center">
            <div class="col-md-7 p-4 p-lg-5 text-white">
                <h2 class="fw-800 mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                <p class="lead mb-4" style="opacity:.8; font-size:16px">
                    @role('siswa') Semangat belajar hari ini di {{ config('school.name') }}. Jangan lupa cek jadwal dan jaga
                    kehadiranmu! @endrole
                    @role('guru') Terus bimbing siswa-siswi kita menuju masa depan gemilang. Selamat bertugas! @endrole
                    @role('admin') Kelola sistem dengan efisien. Semua data master dan konfigurasi ada dalam kendalimu.
                    @endrole
                    @role('kepala_sekolah') Pantau perkembangan sekolah dan absensi siswa secara real-time. @endrole
                </p>
                <div class="d-flex gap-2">
                    <div class="bg-white bg-opacity-10 text-white rounded-3 py-2 px-3 d-flex align-items-center border border-white border-opacity-10">
                        <i class="bi bi-calendar3 me-2 text-warning"></i>
                        <span><span class="text-warning fw-bold">{{ now()->translatedFormat('l') }}</span>, {{ now()->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="bg-primary text-white rounded-3 py-2 px-3 d-flex align-items-center shadow-sm">
                        <i class="bi bi-clock me-2 text-white-50"></i>
                        <span id="live-clock" class="fw-bold">00:00:00</span>
                    </div>
                </div>
            </div>
            <div class="col-md-5 d-none d-md-block"
                style="height: 280px; background: url('/assets/images/dashboard-banner.png') center center; background-size: cover;">
                <div class="h-100 w-100" style="background: linear-gradient(90deg, #0f172a 0%, transparent 100%);"></div>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    @role('admin,guru,kepala_sekolah')
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e8f0fe;color:#1a56db"><i class="bi bi-people-fill"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">
                    {{ number_format($total_students) }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Total Siswa</div>
                <div style="font-size:11px;color:#10b981;margin-top:6px">↑ Aktif semua kelas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-card">
                <div class="stat-icon" style="background:#d1fae5;color:#10b981"><i class="bi bi-check-circle-fill"></i>
                </div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats['hadir'] }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Hadir Hari Ini</div>
                <div style="font-size:11px;color:#10b981;margin-top:6px">{{ $stats['percentage'] }}% kehadiran</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fef3c7;color:#f59e0b"><i class="bi bi-clock-fill"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats['terlambat'] }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Terlambat</div>
                <div style="font-size:11px;color:#f59e0b;margin-top:6px">Hari ini</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fee2e2;color:#ef4444"><i class="bi bi-x-circle-fill"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats['alfa'] }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Tidak Hadir (Alfa)</div>
                <div style="font-size:11px;color:#ef4444;margin-top:6px">Hari ini</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ede9fe;color:#8b5cf6"><i class="bi bi-person-badge-fill"></i>
                </div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $total_teachers }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Total Guru</div>
                <div style="font-size:11px;color:#8b5cf6;margin-top:6px">{{ $total_classes }} Kelas Aktif</div>
            </div>
        </div>
    </div>
    @endrole

    {{-- ── SISWA QUICK STATS ── --}}
    @role('siswa')
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#d1fae5;color:#10b981"><i
                        class="bi bi-check-circle-fill"></i></div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats['hadir'] }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Hadir Hari Ini</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fef3c7;color:#f59e0b"><i class="bi bi-clock-fill"></i>
                </div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats['terlambat'] }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Terlambat</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#fee2e2;color:#ef4444"><i class="bi bi-x-circle-fill"></i>
                </div>
                <div style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace">{{ $stats['alfa'] }}
                </div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Alfa</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto" style="background:#dbeafe;color:#1a56db"><i class="bi bi-percent"></i></div>
                <div
                    style="font-size:28px;font-weight:800;font-family:'JetBrains Mono',monospace;color:{{ $stats['percentage'] >= 75 ? '#10b981' : '#ef4444' }}">
                    {{ $stats['percentage'] }}%</div>
                <div style="color:#64748b;font-size:12px;margin-top:2px">Kehadiran</div>
            </div>
        </div>
    </div>

    {{-- Siswa Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('attendance.qr.scan-page') }}" class="card text-decoration-none border-0 shadow-sm h-100"
                style="border-radius:1rem;background:linear-gradient(135deg,#1a56db,#3b82f6)">
                <div class="card-body text-center py-4 text-white">
                    <i class="bi bi-qr-code-scan" style="font-size:2.5rem"></i>
                    <h6 class="fw-bold mt-3 mb-1">Scan QR Absensi</h6>
                    <p class="mb-0 small" style="opacity:.8">Scan QR dari guru untuk absen</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('attendance.mine') }}" class="card text-decoration-none border-0 shadow-sm h-100"
                style="border-radius:1rem;background:linear-gradient(135deg,#10b981,#34d399)">
                <div class="card-body text-center py-4 text-white">
                    <i class="bi bi-calendar-check" style="font-size:2.5rem"></i>
                    <h6 class="fw-bold mt-3 mb-1">Riwayat Absensi</h6>
                    <p class="mb-0 small" style="opacity:.8">Lihat rekap kehadiran Anda</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('students.my-card') }}" target="_blank"
                class="card text-decoration-none border-0 shadow-sm h-100"
                style="border-radius:1rem;background:linear-gradient(135deg,#8b5cf6,#a78bfa)">
                <div class="card-body text-center py-4 text-white">
                    <i class="bi bi-person-vcard" style="font-size:2.5rem"></i>
                    <h6 class="fw-bold mt-3 mb-1">Kartu Pelajar</h6>
                    <p class="mb-0 small" style="opacity:.8">Lihat & cetak kartu QR Anda</p>
                </div>
            </a>
        </div>
    </div>
    @endrole

    {{-- ── TODAY SCHEDULE (Guru & Siswa) ── --}}
    @if($today_schedules->isNotEmpty())
        <div class="card mb-4 border-0 shadow-sm" style="border-radius:1rem">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar-event me-2 text-primary"></i>Jadwal Hari Ini
                    <span class="text-muted fw-normal ms-2"
                        style="font-size:12px">{{ now()->translatedFormat('l, d F Y') }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-nowrap gap-3 pb-2" style="overflow-x:auto">
                    @foreach($today_schedules as $jadwal)
                        <div class="card border border-light flex-shrink-0"
                            style="width:250px; border-radius:12px; background:#f8fafc">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span
                                        class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill">
                                        <i
                                            class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}
                                    </span>
                                    @if($jadwal->room)
                                        <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $jadwal->room }}</span>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1 text-truncate" title="{{ $jadwal->subject }}">{{ $jadwal->subject }}</h6>

                                <div class="d-flex align-items-center mt-3 pt-3 border-top border-light">
                                    @if(auth()->user()->isSiswa())
                                        <img src="{{ $jadwal->teacher?->photo_url }}" class="rounded-circle me-2" width="24" height="24"
                                            style="object-fit:cover">
                                        <span
                                            class="small text-muted text-truncate">{{ $jadwal->teacher?->name ?? 'Belum ada guru' }}</span>
                                    @else
                                        <div class="bg-primary bg-opacity-10 rounded text-primary text-center fw-bold me-2"
                                            style="width:24px; height:24px; line-height:24px; font-size:10px">
                                            {{ substr($jadwal->class?->name ?? '?', 0, 2) }}
                                        </div>
                                        <span class="small text-muted text-truncate">Kelas {{ $jadwal->class?->name ?? '-' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole(['siswa', 'guru']))
        <div class="card mb-4 border-0 shadow-sm" style="border-radius:1rem">
            <div class="card-body text-center py-4">
                <i class="bi bi-emoji-smile fs-2 d-block mb-2 text-muted"></i>
                <span class="text-muted">Tidak ada jadwal pelajaran hari ini.</span>
            </div>
        </div>
    @endif

    {{-- ── CHARTS ROW (All roles) ── --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-bar-chart-line me-2 text-primary"></i>Tren Absensi Bulanan</span>
                    <select class="form-select form-select-sm" style="width:100px" id="chart-year">
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                        <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                    </select>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-pie-chart me-2 text-primary"></i>Kehadiran Hari Ini</span>
                    <span class="badge bg-primary">{{ $stats['percentage'] }}%</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div style="position: relative; height: 220px; width: 100%;">
                        <canvas id="todayChart"></canvas>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-4 justify-content-center">
                        <span class="badge" style="background:#d1fae5;color:#065f46">✅ Hadir {{ $stats['hadir'] }}</span>
                        <span class="badge" style="background:#fef3c7;color:#92400e">🤒 Sakit {{ $stats['sakit'] }}</span>
                        <span class="badge" style="background:#dbeafe;color:#1e40af">📄 Izin {{ $stats['izin'] }}</span>
                        <span class="badge" style="background:#fee2e2;color:#991b1b">❌ Alfa {{ $stats['alfa'] }}</span>
                        <span class="badge" style="background:#ede9fe;color:#5b21b6">⏰ Terlambat
                            {{ $stats['terlambat'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── CLASS ATTENDANCE + ACTIVITY (Admin/Guru/KepSek only) ── --}}
    @role('admin,guru,kepala_sekolah')
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-building me-2 text-primary"></i>Kehadiran Per Kelas</span>
                    @role('admin,guru,kepala_sekolah')
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Semua</a>
                    @endrole
                </div>
                <div class="card-body">
                    @forelse($class_attendance as $item)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-600 fs-13">{{ $item['class']->name }}</span>
                                    @if($item['done'])
                                        <span class="badge bg-success" style="font-size:9px">✓ Done</span>
                                    @else
                                        <span class="badge bg-warning text-dark" style="font-size:9px">Pending</span>
                                    @endif
                                </div>
                                <span class="fw-700 fs-13">{{ $item['percentage'] }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar
                                            {{ $item['percentage'] >= 90 ? 'bg-success' : ($item['percentage'] >= 75 ? 'bg-warning' : 'bg-danger') }}"
                                    style="width:{{ $item['percentage'] }}%">
                                </div>
                            </div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px">
                                {{ $item['hadir'] }}/{{ $item['total'] }} siswa hadir
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x fs-1"></i>
                            <p class="mt-2 mb-0">Belum ada data absensi hari ini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-clock-history me-2 text-primary"></i>Aktivitas Terbaru</div>
                <div class="card-body" style="padding:0">
                    @forelse($recent_activities as $act)
                        <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                            <div
                                style="width:10px;height:10px;border-radius:50%;background:#10b981;flex-shrink:0;margin-top:5px">
                            </div>
                            <div>
                                <div style="font-size:13.5px">{{ $act['text'] }}</div>
                                <div style="font-size:11.5px;color:#64748b;margin-top:2px">{{ $act['time'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada aktivitas</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endrole

    {{-- ── LOW ATTENDANCE (Admin/KepSek only) ── --}}
    @role('admin,kepala_sekolah')
    @if($low_attendance->isNotEmpty())
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Siswa Kehadiran Rendah Bulan Ini</span>
                <span class="badge bg-danger">Perlu Perhatian</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>% Kehadiran</th>
                            <th>Status</th>
                            @role('admin')
                            <th>Aksi</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($low_attendance as $row)
                            <tr>
                                <td><strong>{{ $row['student']->name }}</strong></td>
                                <td>{{ $row['student']->class?->name }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-1" style="height:6px;width:80px">
                                            <div class="progress-bar {{ $row['percentage'] < 75 ? 'bg-danger' : 'bg-warning' }}"
                                                style="width:{{ $row['percentage'] }}%"></div>
                                        </div>
                                        <strong>{{ $row['percentage'] }}%</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $row['percentage'] < 75 ? 'badge-status-alfa' : 'badge-status-sakit' }}">
                                        {{ $row['percentage'] < 75 ? '❌ Tidak Memenuhi' : '⚠️ Batas' }}
                                    </span>
                                </td>
                                @role('admin')
                                <td>
                                    <a href="{{ route('students.show', $row['student']) }}"
                                        class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                                @endrole
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @endrole

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // ── Monthly Trend Chart ──
        const monthlyCtx = document.getElementById('monthlyChart');
        const monthlyData = @json($monthly_trend);

        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.label),
                datasets: [{
                    label: 'Kehadiran %',
                    data: monthlyData.map(d => d.percent),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,.1)',
                    tension: .4, fill: true, pointRadius: 4,
                }, {
                    label: 'Alfa',
                    data: monthlyData.map(d => d.alfa),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,.05)',
                    tension: .4, fill: true,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { font: { size: 11, family: 'Plus Jakarta Sans' } } } },
                scales: {
                    y: { min: 0, ticks: { font: { size: 11 } } },
                    x: { ticks: { font: { size: 11 } } }
                }
            }
        });

        // ── Today Doughnut Chart ──
        const todayCtx = document.getElementById('todayChart');
        const stats = @json($stats);

        new Chart(todayCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat'],
                datasets: [{
                    data: [stats.hadir, stats.sakit, stats.izin, stats.alfa, stats.terlambat],
                    backgroundColor: ['#10b981', '#f59e0b', '#1a56db', '#ef4444', '#8b5cf6'],
                    borderWidth: 3, borderColor: '#fff',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });

        // ── Year selector (fixed: references monthlyChart variable) ──
        document.getElementById('chart-year').addEventListener('change', function () {
            fetch(`/dashboard/chart-data?year=${this.value}`)
                .then(r => r.json())
                .then(data => {
                    monthlyChart.data.labels = data.map(d => d.label);
                    monthlyChart.data.datasets[0].data = data.map(d => d.percent);
                    monthlyChart.data.datasets[1].data = data.map(d => d.alfa);
                    monthlyChart.update();
                });
        });
    </script>
@endpush