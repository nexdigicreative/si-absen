@extends('layouts.app')
@section('title', 'Scanner Sekolah')
@section('page-title', 'Scanner Kartu Pelajar')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-upc-scan me-2"></i>Arahkan Kartu ke Kamera</h5>
                <p class="text-muted small mt-2">Pastikan QR Code tegak lurus dan mendapat cahaya yang cukup.</p>
            </div>
            <div class="card-body pb-4">
                <div id="reader" style="width: 100%; border-radius: 12px; overflow: hidden; border: 2px dashed #cbd5e1;"></div>
                <div id="scan-result" class="mt-4 pt-3 border-top d-none">
                    <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
                    <p class="fw-semibold text-primary">Memproses data...</p>
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
    const scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} });

    scanner.render((decodedText, decodedResult) => {
        if (isScanning) return;
        isScanning = true;
        
        document.getElementById('scan-result').classList.remove('d-none');
        document.getElementById('scan-result').innerHTML = `<div class="spinner-border text-primary spinner-border-sm mb-2"></div><p class="fw-semibold text-primary">Memproses...</p>`;

        // Pause scanning
        scanner.pause(true);

        $.post("{{ route('attendance.scan-card') }}", { qr_code: decodedText })
            .done((res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message, timer: 3000, showConfirmButton: false });
                }
            })
            .fail(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal terhubung ke server.', timer: 2000, showConfirmButton: false });
            })
            .always(() => {
                document.getElementById('scan-result').classList.add('d-none');
                setTimeout(() => { 
                    isScanning = false; 
                    scanner.resume(); 
                }, 2500); // 2.5 seconds debounce
            });
    });
</script>
@endpush
