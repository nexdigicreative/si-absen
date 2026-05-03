<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Now values are populated by AppServiceProvider through config() dynamically from DB.
        $settings = [
            'school_name'      => config('school.name', 'Nama Sekolah'),
            'school_address'   => config('school.address', ''),
            'school_phone'     => config('school.phone', ''),
            'school_email'     => config('school.email', ''),
            'school_logo'      => config('school.logo', ''),
            'late_limit'       => config('attendance.late_limit', '07:15'),
            'min_attendance'   => config('attendance.min_percentage', 75),
            'academic_year'    => config('school.academic_year', date('Y') . '/' . (date('Y') + 1)),
        ];

        return view('settings.index', compact('settings'));
    }

    public function updateSchool(Request $request)
    {
        $data = $request->validate([
            'school_name'    => 'required|string|max:150',
            'school_address' => 'nullable|string|max:300',
            'school_phone'   => 'nullable|string|max:30',
            'school_email'   => 'nullable|email|max:100',
            'academic_year'  => 'nullable|string|max:20',
        ]);

        Setting::set('school_name', $data['school_name'], 'school');
        Setting::set('school_address', $data['school_address'] ?? '', 'school');
        Setting::set('school_phone', $data['school_phone'] ?? '', 'school');
        Setting::set('school_email', $data['school_email'] ?? '', 'school');
        Setting::set('school_academic_year', $data['academic_year'] ?? '', 'school');

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan sekolah berhasil disimpan.');
    }

    public function updateAttendance(Request $request)
    {
        $data = $request->validate([
            'late_limit'     => 'required|date_format:H:i',
            'min_attendance' => 'required|integer|min:1|max:100',
        ]);

        Setting::set('att_late_limit', $data['late_limit'] . ':00', 'attendance');
        Setting::set('att_min_pct', $data['min_attendance'], 'attendance');

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan absensi berhasil disimpan.');
    }
}
