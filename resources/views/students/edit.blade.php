{{-- resources/views/students/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Edit data siswa: <strong>{{ $student->name }}</strong></p>
    <div class="d-flex gap-2">
        <a href="{{ route('students.show', $student) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-1"></i>Detail
        </a>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-pencil-square me-2 text-primary"></i>Data Siswa</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror"
                                value="{{ old('nis', $student->nis) }}" required>
                            @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NISN</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn', $student->nisn) }}">
                            @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $student->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="dob" class="form-control"
                                value="{{ old('dob', $student->dob?->toDateString()) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', $student->status) ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !old('status', $student->status) ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Orang Tua</label>
                            <input type="text" name="parent_name" class="form-control"
                                value="{{ old('parent_name', $student->parent_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP Orang Tua</label>
                            <input type="text" name="parent_phone" class="form-control"
                                value="{{ old('parent_phone', $student->parent_phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Orang Tua</label>
                            <input type="email" name="parent_email" class="form-control"
                                value="{{ old('parent_email', $student->parent_email) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-camera me-2 text-primary"></i>Foto Siswa</div>
                <div class="card-body text-center">
                    <div id="photo-preview" style="width:120px;height:120px;border-radius:50%;margin:0 auto 12px;overflow:hidden;border:3px dashed #1a56db">
                        <img src="{{ $student->photo_url }}" style="width:100%;height:100%;object-fit:cover" alt="">
                    </div>
                    <input type="file" name="photo" id="photo-input" class="d-none" accept="image/*"
                        onchange="previewPhoto(this)">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('photo-input').click()">
                        <i class="bi bi-upload me-1"></i>Ganti Foto
                    </button>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
                <form id="del-{{ $student->id }}" method="POST" action="{{ route('students.destroy', $student) }}">
                    @csrf @method('DELETE')
                </form>
                <button type="button" class="btn btn-outline-danger"
                    data-confirm="Hapus siswa {{ $student->name }}?" data-form="del-{{ $student->id }}">
                    <i class="bi bi-trash me-2"></i>Hapus Siswa
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photo-preview').innerHTML =
                `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
