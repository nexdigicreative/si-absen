@extends('layouts.app')
@section('title', 'Monitor Absensi Kelas')
@section('page-title', 'Monitor Absensi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-broadcast me-2 text-danger blink"></i> Live Data Absensi
                </h5>
                <span class="badge bg-primary" id="time-badge">--:--:--</span>
            </div>
            <div class="card-body">
                @if(!$classId)
                    <div class="alert alert-warning">Pilih kelas terlebih dahulu.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu Scan</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="live-table-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .blink { animation: blink-animation 1s steps(5, start) infinite; }
    @keyframes blink-animation { to { visibility: hidden; } }
</style>
@endpush

@push('scripts')
<script>
    function updateLiveTime() {
        const now = new Date();
        document.getElementById('time-badge').innerText = now.toLocaleTimeString('id-ID');
    }
    
    function fetchLiveData() {
        @if($classId)
        $.get("{{ route('attendance.qr.live', ['class_id' => $classId]) }}")
            .done((data) => {
                let html = '';
                if(data.length === 0) {
                    html = '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada siswa yang absen.</td></tr>';
                } else {
                    data.forEach(item => {
                        let statusColor = item.status === 'hadir' ? 'success' : (item.status === 'terlambat' ? 'warning' : 'danger');
                        html += `
                            <tr>
                                <td class="fw-semibold text-primary">${item.time}</td>
                                <td><code>${item.nis}</code></td>
                                <td class="fw-bold">${item.name}</td>
                                <td><span class="badge bg-${statusColor}">${item.status.toUpperCase()}</span></td>
                            </tr>
                        `;
                    });
                }
                $('#live-table-body').html(html);
            });
        @endif
    }
    
    setInterval(updateLiveTime, 1000);
    setInterval(fetchLiveData, 3000); // Fetch every 3 seconds
    fetchLiveData();
</script>
@endpush
