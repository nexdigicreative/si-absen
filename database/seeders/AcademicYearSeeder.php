<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::create([
            'year' => '2024/2025',
            'semester' => 2,
            'start_date' => '2025-01-06',
            'end_date' => '2025-06-14',
            'is_active' => true,
        ]);

        AcademicYear::create([
            'year' => '2024/2025',
            'semester' => 1,
            'start_date' => '2024-07-15',
            'end_date' => '2024-12-21',
            'is_active' => false,
        ]);
    }
}