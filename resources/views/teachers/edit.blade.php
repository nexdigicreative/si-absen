{{-- resources/views/teachers/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Edit data guru: <strong>{{ $teacher->name }}</strong></p>
    <div class="d-flex gap-2">
        <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-info"><i class="bi bi-eye me-1"></i>Detail</a>
        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Guru</div>
            <div class="card-body">
                <form method="POST" action="{{ route('teachers.update', $teacher) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                value="{{ old('nip', $teacher->nip) }}">
                            @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $teacher->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Pelajaran</label>
                            <input type="text" name="subject" class="form-control"
                                value="{{ old('subject', $teacher->subject) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $teacher->phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $teacher->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ganti Foto</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', $teacher->status) ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !old('status', $teacher->status) ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                        <form id="del-t{{ $teacher->id }}" method="POST" action="{{ route('teachers.destroy', $teacher) }}">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="btn btn-outline-danger"
                            data-confirm="Hapus guru {{ $teacher->name }}?" data-form="del-t{{ $teacher->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
