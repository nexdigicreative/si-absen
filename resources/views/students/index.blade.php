{{-- resources/views/students/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <p class="text-muted mb-0">Total: <strong>{{ $students->total() }}</strong> siswa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Siswa
            </a>
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import Excel
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama, NIS..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="gender" class="form-select">
                        <option value="">Semua JK</option>
                        <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
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
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>JK</th>
                        <th>Tgl Lahir</th>
                        <th>No. Ortu</th>
                        <th>% Hadir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $i => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $i }}</td>
                            <td><code>{{ $student->nis }}</code></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $student->photo_url }}" alt="" width="32" height="32"
                                        style="border-radius:50%;object-fit:cover">
                                    <strong>{{ $student->name }}</strong>
                                </div>
                            </td>
                            <td><span class="badge" style="background:#e8f0fe;color:#1a56db">{{ $student->class?->name }}</span>
                            </td>
                            <td>
                                @if($student->gender === 'L')
                                    <span class="badge" style="background:#dbeafe;color:#1e40af">L</span>
                                @else
                                    <span class="badge" style="background:#fce7f3;color:#9d174d">P</span>
                                @endif
                            </td>
                            <td style="font-size:12px">{{ $student->dob?->format('d/m/Y') ?? '-' }}</td>
                            <td style="font-size:12px">{{ $student->parent_phone ?? '-' }}</td>
                            <td>
                                @php $pct = $student->attendance_percentage @endphp
                                <div class="d-flex align-items-center gap-1">
                                    <div class="progress" style="width:50px;height:5px">
                                        <div class="progress-bar {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}"
                                            style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span style="font-size:11px;font-weight:700">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $student->status ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $student->status ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-info"
                                        title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary"
                                        title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form id="del-{{ $student->id }}" method="POST"
                                        action="{{ route('students.destroy', $student) }}">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger"
                                        data-confirm="Hapus siswa {{ $student->name }}?" data-form="del-{{ $student->id }}"
                                        title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>Tidak ada siswa ditemukan.
                                <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary mt-2">+ Tambah Siswa</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted" style="font-size:13px">Menampilkan
                {{ $students->firstItem() }}-{{ $students->lastItem() }} dari {{ $students->total() }}</div>
            {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📥 Import Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Format Excel:</strong> Kolom: NIS, NISN, Nama, JK (L/P), Tanggal_Lahir, Kelas,
                            Nama_Ortu, No_HP_Ortu
                        </div>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- ============================================================ --}}
{{-- resources/views/reports/index.blade.php --}}
{{-- ============================================================ --}}
{{-- @extends('layouts.app')
@section('title','Laporan Absensi')
@section('page-title','Laporan Absensi')
Implemented as a full page with filters and charts --}}