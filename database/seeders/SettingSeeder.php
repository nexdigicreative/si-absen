<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['school_name', 'SMA Jakarta', 'school'],
            ['school_npsn', '20109988', 'school'],
            ['school_address', 'Jl. Sudirman No. 1, Jakarta Pusat', 'school'],
            ['school_phone', '021-12345678', 'school'],
            ['school_email', 'info@smajakarta.sch.id', 'school'],
            ['school_principal', 'Dra. Hj. Siti Rahayu, M.Pd', 'school'],
            ['att_start_time', '07:00', 'attendance'],
            ['att_late_limit', '07:15', 'attendance'],
            ['att_end_time', '15:30', 'attendance'],
            ['att_min_pct', '75', 'attendance'],
            ['att_working_days', '22', 'attendance'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }
    }
}