<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrAttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.active'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart');

    /*
    |----------------------------------------------------------------------
    | Students — Admin only
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('students', StudentController::class);
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('students/{student}/attendance-history', [StudentController::class, 'attendanceHistory'])
            ->name('students.attendance-history');
        Route::get('students/{student}/print-card', [StudentController::class, 'printCard'])
            ->name('students.print-card');

        Route::resource('teachers', TeacherController::class);
        Route::resource('classes', ClassController::class);
        Route::resource('users', UserController::class);
        Route::put('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        Route::put('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/update-school', [SettingController::class, 'updateSchool'])
            ->name('settings.school');
        Route::post('settings/update-attendance', [SettingController::class, 'updateAttendance'])
            ->name('settings.attendance');
    });

    /*
    |----------------------------------------------------------------------
    | Attendance — Admin & Guru (manage)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,guru')->group(function () {
        Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');

        // QR Code — Generate & Monitor (guru/admin only)
        Route::get('attendance/qr/generate', [QrAttendanceController::class, 'generate'])->name('attendance.qr.generate');
        Route::get('attendance/qr/monitor', [QrAttendanceController::class, 'monitor'])->name('attendance.qr.monitor');
        Route::get('attendance/qr/live-data', [QrAttendanceController::class, 'liveData'])->name('attendance.qr.live');

        // Scanner Sekolah (Admin/Guru scan kartu pelajar)
        Route::get('attendance/scanner', [QrAttendanceController::class, 'scanner'])->name('attendance.scanner');
        Route::post('attendance/scan-card', [QrAttendanceController::class, 'processCardScan'])->name('attendance.scan-card');

        // Schedule — Create & Delete (guru/admin only)
        Route::get('schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    // Attendance & Schedule — Viewable by ALL authenticated users
    Route::middleware('role:admin,guru,siswa,kepala_sekolah')->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    });

    // QR Scan — Students & Guru
    Route::middleware('role:siswa,guru,admin')->group(function () {
        Route::get('qr/scan', [QrAttendanceController::class, 'scanPage'])->name('attendance.qr.scan-page');
        Route::post('qr/scan', [QrAttendanceController::class, 'processScan'])->name('attendance.qr.scan');
    });

    /*
    |----------------------------------------------------------------------
    | Reports — Admin & Kepala Sekolah & Guru
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,kepala_sekolah,guru')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/recap', [ReportController::class, 'recap'])->name('reports.recap');
        Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    });

    /*
    |----------------------------------------------------------------------
    | Student Portal — Siswa view own attendance & schedule
    |----------------------------------------------------------------------
    */
    Route::middleware('role:siswa')->group(function () {
        Route::get('my-attendance', [AttendanceController::class, 'myAttendance'])->name('attendance.mine');
        Route::get('my-card', [StudentController::class, 'myCard'])->name('students.my-card');
    });

    /*
    |----------------------------------------------------------------------
    | Profile (all roles)
    |----------------------------------------------------------------------
    */
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [UserController::class, 'updatePassword'])->name('profile.password');
});