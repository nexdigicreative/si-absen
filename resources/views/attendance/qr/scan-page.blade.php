@extends('layouts.app')
@section('title', 'Scan QR Kelas')
@section('page-title', 'Scan Kehadiran')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-qr-code-scan me-2"></i>Scan QR Dari Guru</h5>
                <p class="text-muted small mt-2">Arahkan kamera ke QR Code yang ditampilkan oleh guru Anda.</p>
            </div>
            <div class="card-body pb-4">
                <div id="reader" style="width: 100%; border-radius: 12px; overflow: hidden; border: 2px dashed #cbd5e1;"></div>
                <div id="scan-result" class="mt-4 pt-3 border-top d-none">
                    <p class="fw-semibold text-primary mb-0">Memproses kehadiran Anda...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let isScanning = false;
    const scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);

    scanner.render((decodedText, decodedResult) => {
        if (isScanning) return;
        isScanning = true;
        
        document.getElementById('scan-result').classList.remove('d-none');
        scanner.pause(true);

        $.post("{{ route('attendance.qr.scan') }}", { qr_code: decodedText })
            .done((res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil Absen', text: res.message, timer: 3000, showConfirmButton: false })
                        .then(() => window.location.href = "{{ route('attendance.mine') }}");
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message, timer: 3000, showConfirmButton: true });
                    setTimeout(() => { isScanning = false; scanner.resume(); }, 3000);
                }
            })
            .fail(() => {
                Swal.fire({ icon: 'error', title: 'Kesalahan Server', text: 'Gagal memproses data.', timer: 2000, showConfirmButton: false });
                setTimeout(() => { isScanning = false; scanner.resume(); }, 2000);
            });
    });
</script>
@endpush
