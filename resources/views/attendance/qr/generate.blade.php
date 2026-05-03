@extends('layouts.app')
@section('title', 'Generate QR Kelas')
@section('page-title', 'QR Code Hari Ini')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-qr-code me-2"></i>Pilih Kelas</h5>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select class="form-select @error('class_id') is-invalid @enderror" name="class_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buat QR Baru</button>
                </form>
            </div>
        </div>
    </div>
    
    @if($classId && $qrString)
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100 text-center">
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                <h4 class="fw-bold text-dark mb-1">Scan QR Code Berikut</h4>
                <p class="text-muted mb-4">Silakan minta siswa untuk scan dari Portal mereka.</p>
                
                <div class="p-3 bg-white border rounded shadow-sm" style="display:inline-block">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->margin(1)->style('round')->generate($qrString) !!}
                </div>
                
                <div class="mt-4">
                    <span class="badge bg-primary fs-6">{{ $classes->where('id', $classId)->first()->name }}</span>
                    <span class="badge bg-secondary fs-6 ms-2">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span>
                </div>
                
                <a href="{{ route('attendance.qr.monitor', ['class_id' => $classId]) }}" class="btn btn-outline-primary mt-4">
                    <i class="bi bi-eye"></i> Buka Monitor Absensi
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
