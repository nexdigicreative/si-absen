<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['school_name', 'SMAN 1 Bandung', 'school'],
            ['school_npsn', '20219388', 'school'],
            ['school_address', 'Jl. Ir. H. Juanda No. 93, Bandung', 'school'],
            ['school_phone', '022-4232807', 'school'],
            ['school_email', 'info@sman1bdg.sch.id', 'school'],
            ['school_principal', 'Dr. H. Wahyu Santoso, M.Pd', 'school'],
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