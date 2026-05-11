{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – SIABSEN</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    {{-- SweetAlert2 --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-font-sans-serif: 'Plus Jakarta Sans', system-ui, sans-serif;
            --primary: #1a56db;
            --sidebar-w: 260px;
            --header-h: 60px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4ff;
            font-size: 14px;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transition: transform .4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Thin scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .sidebar-logo {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
        }

        .logo-text {
            color: #fff;
            font-weight: 700;
            font-size: 14px;
        }

        .logo-sub {
            color: rgba(255, 255, 255, .4);
            font-size: 10px;
            letter-spacing: .5px;
        }

        .school-badge {
            margin: 10px 14px;
            background: rgba(255, 255, 255, .05);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .school-badge-name {
            color: rgba(255, 255, 255, .85);
            font-size: 12px;
            font-weight: 600;
        }

        .school-badge-type {
            color: rgba(255, 255, 255, .4);
            font-size: 10px;
        }

        .nav-section {
            padding: 6px 0;
        }

        .nav-label {
            padding: 8px 20px 3px;
            font-size: 9.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .3);
            font-weight: 600;
        }

        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: rgba(255, 255, 255, .55);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .15s;
            white-space: nowrap;
        }

        .nav-link-item:hover {
            background: rgba(255, 255, 255, .06);
            color: rgba(255, 255, 255, .9);
        }

        .nav-link-item.active {
            background: rgba(26, 86, 219, .25);
            color: #fff;
            border-left-color: var(--primary);
        }

        .nav-link-item .nav-icon {
            font-size: 15px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 99px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 12px 14px 16px;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .user-pill {
            background: rgba(255, 255, 255, .06);
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            display: grid;
            place-items: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .user-name {
            color: rgba(255, 255, 255, .9);
            font-size: 12.5px;
            font-weight: 600;
        }

        .user-role {
            color: rgba(255, 255, 255, .4);
            font-size: 10.5px;
        }

        /* ── MAIN CONTENT ── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            height: var(--header-h);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            z-index: 1040;
            backdrop-filter: blur(4px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .topbar-page-title {
            font-size: 17px;
            font-weight: 800;
            flex: 1;
        }

        .date-badge {
            background: #e8f0fe;
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
        }

        .topbar-btn {
            width: 36px;
            height: 36px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            display: grid;
            place-items: center;
            color: #64748b;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }

        .topbar-btn:hover {
            background: #e8f0fe;
            border-color: var(--primary);
            color: var(--primary);
        }

        /* ── PAGE CONTENT ── */
        .page-content {
            padding: 24px;
            flex: 1;
        }

        /* ── STAT CARDS ── */
        .stat-card {
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            padding: 24px;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            border-color: var(--primary);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        /* ── CARDS ── */
        .card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06) !important;
        }

        .card-header {
            background: #f8faff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            font-weight: 700;
            font-size: 14px;
        }

        /* ── TABLE ── */
        .table th {
            background: #f8faff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
        }

        .table td {
            vertical-align: middle;
        }

        /* ── BADGES ── */
        .badge-status-hadir {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-status-sakit {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-status-izin {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-status-alfa {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-status-terlambat {
            background: #ede9fe;
            color: #5b21b6;
        }

        /* ── ATTENDANCE INPUT ── */
        .status-group .btn-check:checked+.btn {
            font-weight: 600;
        }

        .att-row {
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 0;
        }

        .att-row:last-child {
            border-bottom: none;
        }

        .student-num {
            width: 28px;
            height: 28px;
            background: #e8f0fe;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            flex-shrink: 0;
        }

        /* ── PROGRESS ── */
        .progress {
            height: 8px;
            border-radius: 99px;
        }

        /* ── CODE ── */
        code {
            background: #f1f5f9;
            color: var(--primary);
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 12px;
        }

        /* ── ALERTS ── */
        .alert {
            border-radius: 10px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0,0,0,0.1);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
            
            .topbar {
                padding: 0 16px;
            }
            
            .page-content {
                padding: 16px;
            }
        }
        
        /* Make mobile layout compact so less scrolling needed */
        @media (max-height: 700px) {
            .nav-link-item {
                padding: 7px 20px;
                font-size: 12px;
            }
            .sidebar-logo, .school-badge {
                padding-top: 10px;
                padding-bottom: 10px;
            }
            .school-badge { margin: 6px 14px; }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- SIDEBAR OVERLAY --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="d-flex align-items-center gap-2">
                <div class="logo-icon">S</div>
                <div>
                    <div class="logo-text">SIABSEN</div>
                    <div class="logo-sub">v3.0 · {{ date('Y') }}</div>
                </div>
            </div>
        </div>

        <div class="school-badge">
            <div class="d-flex align-items-center gap-2">
                <span style="font-size:20px">🏫</span>
                <div>
                    <div class="school-badge-name">{{ config('school.name', 'SMA Jakarta') }}</div>
                    <div class="school-badge-type">SMA</div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
            UTAMA — Semua role
        ═══════════════════════════════════════════ --}}
        <div class="nav-section">
            <div class="nav-label">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link-item @active('dashboard')">
                <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
            </a>
        </div>

        {{-- ═══════════════════════════════════════════
            PORTAL SISWA — Siswa only
        ═══════════════════════════════════════════ --}}
        @role('siswa')
            <div class="nav-section">
                <div class="nav-label">Portal Siswa</div>
                <a href="{{ route('attendance.mine') }}" class="nav-link-item @active('attendance.mine')">
                    <i class="bi bi-calendar-check nav-icon"></i> Absensi Saya
                </a>
                <a href="{{ route('students.my-card') }}" target="_blank" class="nav-link-item">
                    <i class="bi bi-person-vcard nav-icon"></i> Kartu Pelajar (QR)
                </a>
                <a href="{{ route('attendance.qr.scan-page') }}" class="nav-link-item @active('attendance.qr.scan-page')">
                    <i class="bi bi-qr-code-scan nav-icon"></i> Scan QR Absensi
                </a>
                <a href="{{ route('schedules.index') }}" class="nav-link-item @active('schedules.*')">
                    <i class="bi bi-calendar3 nav-icon"></i> Jadwal Pelajaran
                </a>
                <a href="{{ route('attendance.index') }}" class="nav-link-item @active('attendance.index')">
                    <i class="bi bi-clipboard-check nav-icon"></i> Daftar Absensi
                </a>
            </div>
        @endrole

        {{-- ═══════════════════════════════════════════
            ABSENSI — Admin & Guru
        ═══════════════════════════════════════════ --}}
        @role('admin,guru')
            <div class="nav-section">
                <div class="nav-label">Absensi</div>
                <a href="{{ route('attendance.create') }}" class="nav-link-item @active('attendance.create')">
                    <i class="bi bi-check2-square nav-icon"></i> Input Absensi
                    <span class="nav-badge">Hari Ini</span>
                </a>
                <a href="{{ route('attendance.index') }}" class="nav-link-item @active('attendance.index')">
                    <i class="bi bi-clipboard-check nav-icon"></i> Daftar Absensi
                </a>
                <a href="{{ route('attendance.qr.generate') }}" class="nav-link-item @active('attendance.qr.*')">
                    <i class="bi bi-qr-code nav-icon"></i> Generate QR Kelas
                </a>
                <a href="{{ route('attendance.scanner') }}" class="nav-link-item @active('attendance.scanner')">
                    <i class="bi bi-upc-scan nav-icon"></i> Scanner Sekolah
                </a>
            </div>
        @endrole

        {{-- ═══════════════════════════════════════════
            JADWAL — Admin & Guru
        ═══════════════════════════════════════════ --}}
        @role('admin,guru')
            <div class="nav-section">
                <div class="nav-label">Jadwal</div>
                <a href="{{ route('schedules.index') }}" class="nav-link-item @active('schedules.*')">
                    <i class="bi bi-calendar3 nav-icon"></i> Jadwal Pelajaran
                </a>
            </div>
        @endrole

        {{-- ═══════════════════════════════════════════
            MONITORING — Kepala Sekolah
        ═══════════════════════════════════════════ --}}
        @role('kepala_sekolah')
            <div class="nav-section">
                <div class="nav-label">Monitoring</div>
                <a href="{{ route('attendance.index') }}" class="nav-link-item @active('attendance.index')">
                    <i class="bi bi-clipboard-check nav-icon"></i> Daftar Absensi
                </a>
                <a href="{{ route('schedules.index') }}" class="nav-link-item @active('schedules.*')">
                    <i class="bi bi-calendar3 nav-icon"></i> Jadwal Pelajaran
                </a>
            </div>
        @endrole

        {{-- ═══════════════════════════════════════════
            DATA MASTER — Admin only
        ═══════════════════════════════════════════ --}}
        @role('admin')
            <div class="nav-section">
                <div class="nav-label">Data Master</div>
                <a href="{{ route('students.index') }}" class="nav-link-item @active('students.*')">
                    <i class="bi bi-people nav-icon"></i> Data Siswa
                </a>
                <a href="{{ route('teachers.index') }}" class="nav-link-item @active('teachers.*')">
                    <i class="bi bi-person-badge nav-icon"></i> Data Guru
                </a>
                <a href="{{ route('classes.index') }}" class="nav-link-item @active('classes.*')">
                    <i class="bi bi-building nav-icon"></i> Data Kelas
                </a>
            </div>
        @endrole

        {{-- ═══════════════════════════════════════════
            LAPORAN — Admin, Guru, Kepala Sekolah
        ═══════════════════════════════════════════ --}}
        @role('admin,guru,kepala_sekolah')
            <div class="nav-section">
                <div class="nav-label">Laporan</div>
                <a href="{{ route('reports.index') }}" class="nav-link-item @active('reports.index')">
                    <i class="bi bi-bar-chart nav-icon"></i> Laporan Absensi
                </a>
                <a href="{{ route('reports.daily') }}" class="nav-link-item @active('reports.daily')">
                    <i class="bi bi-calendar-date nav-icon"></i> Laporan Harian
                </a>
                <a href="{{ route('reports.recap') }}" class="nav-link-item @active('reports.recap')">
                    <i class="bi bi-table nav-icon"></i> Rekap Bulanan
                </a>
            </div>
        @endrole

        {{-- ═══════════════════════════════════════════
            MANAJEMEN — Admin only
        ═══════════════════════════════════════════ --}}
        @role('admin')
            <div class="nav-section">
                <div class="nav-label">Manajemen</div>
                <a href="{{ route('users.index') }}" class="nav-link-item @active('users.*')">
                    <i class="bi bi-shield-person nav-icon"></i> Manajemen User
                </a>
                <a href="{{ route('settings.index') }}" class="nav-link-item @active('settings.*')">
                    <i class="bi bi-gear nav-icon"></i> Pengaturan
                </a>
            </div>
        @endrole

        <div class="sidebar-footer">
            <div class="user-pill mb-2">
                <img src="{{ auth()->user()->avatar_url }}" class="user-avatar" alt="Avatar">
                <div class="flex-1">
                    <div class="user-name">{{ Str::limit(auth()->user()->name, 20) }}</div>
                    <div class="user-role">{{ auth()->user()->getRoleLabel() }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100"
                    style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6);border:1px solid rgba(255,255,255,.12);font-size:12px">
                    <i class="bi bi-box-arrow-left me-1"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="main-wrapper">
        {{-- TOPBAR --}}
        <header class="topbar">
            <button class="topbar-btn d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-page-title">@yield('page-title', 'Dashboard')</div>
            <div class="date-badge">{{ now()->translatedFormat('D, d M Y') }}</div>
            <a href="{{ route('profile') }}" class="topbar-btn" title="Profil">
                <i class="bi bi-person"></i>
            </a>
            @role('admin')
                <a href="{{ route('settings.index') }}" class="topbar-btn" title="Pengaturan">
                    <i class="bi bi-gear"></i>
                </a>
            @endrole
        </header>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error') || $errors->any())
            <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                @if(session('error')){{ session('error') }}@else{{ $errors->first() }}@endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- PAGE CONTENT --}}
        <main class="page-content">
            @yield('content')
        </main>

        <footer class="text-center py-3 text-muted" style="font-size:12px;border-top:1px solid #e2e8f0">
            SIABSEN &copy; {{ date('Y') }} · {{ config('school.name') }} · v3.0
        </footer>
    </div>

    {{-- Scripts — Core only --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Global CSRF setup for AJAX
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Sidebar Toggle for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Confirm delete
        document.querySelectorAll('[data-confirm]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const form = document.getElementById(this.dataset.form);
                Swal.fire({
                    title: 'Konfirmasi',
                    text: this.dataset.confirm || 'Apakah Anda yakin?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                }).then(r => { if (r.isConfirmed && form) form.submit(); });
            });
        });

        // Auto-hide flash
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(a => {
                bootstrap.Alert.getOrCreateInstance(a)?.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>