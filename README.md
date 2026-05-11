# SIABSEN v3.0 – SMA Jakarta

SIABSEN adalah Sistem Informasi Absensi Siswa berbasis web yang modern, cepat, dan aman. Dikembangkan menggunakan Laravel 11, sistem ini dirancang untuk mendigitalisasi proses kehadiran di sekolah melalui metode Manual, QR Code, dan Monitoring Real-time.

## 🚀 Fitur Utama

- **Multi-Role Access Control**: Admin, Guru, Siswa, dan Kepala Sekolah memiliki dashboard dan akses yang berbeda.
- **Hybrid Attendance**: Mendukung absensi manual oleh guru dan absensi mandiri siswa via QR Code.
- **Live Monitoring**: Dashboard khusus untuk memantau kehadiran siswa secara real-time.
- **Reporting System**: Rekapitulasi bulanan otomatis yang dapat diekspor ke format PDF dan Excel.
- **Profile & Card Management**: Cetak kartu pelajar dengan QR Code terenkripsi.
- **Performance Optimized**: Menggunakan teknik *Batch Upsert* untuk penanganan data skala besar.

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade, Bootstrap 5, Chart.js
- **Database**: MySQL / MariaDB
- **Tools**: DomPDF, Maatwebsite Excel, Simple QR Code

## 📦 Instalasi

1. Clone repository:
   ```bash
   git clone https://github.com/nexdigicreative/si-absen.git
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
3. Konfigurasi Environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Migrasi & Seed Database:
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan server:
   ```bash
   php artisan serve
   ```

## 🔒 Keamanan & Optimasi (v3.0)

- **QR Encryption**: Payload QR Code dienkripsi menggunakan AES-256 untuk mencegah pemalsuan absen.
- **Rate Limiting**: Melindungi sistem dari serangan brute force pada halaman login.
- **Query Optimization**: Implementasi indexing pada kolom tanggal dan optimasi aggregasi data.

---
SIABSEN © 2026 · **SMA Jakarta** · Developed by NexDigi System
