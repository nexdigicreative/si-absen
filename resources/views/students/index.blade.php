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

    <div class="card mb-4 border-0 shadow-sm" style="border-radius:1rem">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted ms-1">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, NIS, atau NISN..."
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted ms-1">Filter Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted ms-1">Jenis Kelamin</label>
                    <select name="gender" class="form-select">
                        <option value="">Semua</option>
                        <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted ms-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-funnel"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:1rem; overflow:hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-bold small">NIS</th>
                        <th class="py-3 text-muted fw-bold small">NAMA SISWA</th>
                        <th class="py-3 text-muted fw-bold small">KELAS</th>
                        <th class="py-3 text-muted fw-bold small">JK</th>
                        <th class="py-3 text-muted fw-bold small">ORTU / HP</th>
                        <th class="py-3 text-muted fw-bold small text-center">% HADIR</th>
                        <th class="py-3 text-muted fw-bold small">STATUS</th>
                        <th class="pe-4 py-3 text-muted fw-bold small text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="ps-4">
                                <code class="bg-light px-2 py-1 rounded text-primary fw-bold" style="font-size:12px">{{ $student->nis }}</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $student->photo_url }}" alt="" width="38" height="38"
                                        class="rounded-circle shadow-sm" style="object-fit:cover; border:2px solid #fff">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $student->name }}</div>
                                        <div class="text-muted small">NISN: {{ $student->nisn ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill">{{ $student->class?->name }}</span></td>
                            <td>
                                @if($student->gender === 'L')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill">L</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill">P</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-600 text-dark">{{ $student->parent_name ?? '-' }}</div>
                                <div class="text-muted small"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $student->parent_phone ?? '-' }}</div>
                            </td>
                            <td>
                                @php $pct = $student->attendance_percentage @endphp
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <div class="progress w-100" style="height:6px; max-width:80px">
                                        <div class="progress-bar {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}"
                                            style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span class="fw-800 text-dark" style="font-size:11px">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($student->status)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill" style="font-size:10px">AKTIF</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill" style="font-size:10px">NON-AKTIF</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-white text-info"
                                        title="Detail"><i class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-white text-primary"
                                        title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                    <button class="btn btn-sm btn-white text-danger delete-btn"
                                        data-confirm="Hapus siswa {{ $student->name }}?" 
                                        data-form="del-{{ $student->id }}"
                                        title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                                <form id="del-{{ $student->id }}" method="POST"
                                    action="{{ route('students.destroy', $student) }}" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-people text-muted opacity-25" style="font-size: 5rem"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada data siswa ditemukan</h5>
                                    <p class="text-muted small">Coba sesuaikan pencarian atau filter Anda.</p>
                                    <a href="{{ route('students.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah Siswa Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3">
            <div class="text-muted small">Menampilkan <strong>{{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }}</strong> dari <strong>{{ $students->total() }}</strong> siswa</div>
            {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1.5rem">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-800">📥 Import Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert bg-primary bg-opacity-10 text-primary border-0 rounded-4 mb-4">
                            <div class="d-flex gap-3">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                                <div class="small">
                                    <strong>Instruksi Format Excel:</strong><br>
                                    Gunakan kolom berikut secara berurutan:<br>
                                    <code>NIS, NISN, Nama, JK (L/P), Tanggal_Lahir, Kelas, Nama_Ortu, No_HP_Ortu</code>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Pilih File Excel/CSV</label>
                            <div class="border rounded-4 p-4 text-center bg-light bg-opacity-50 border-dashed">
                                <i class="bi bi-file-earmark-excel fs-1 text-success mb-2 d-block"></i>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                <p class="text-muted small mt-2 mb-0">Maksimal ukuran file: 2MB</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4 py-2" style="border-radius:10px" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 py-2" style="border-radius:10px">
                            <i class="bi bi-cloud-upload me-1"></i> Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection