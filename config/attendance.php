<?php

return [
    'start_time' => env('ATTENDANCE_START_TIME', '07:00'),
    'late_limit' => env('ATTENDANCE_LATE_LIMIT', '07:15'),
    'end_time' => env('ATTENDANCE_END_TIME', '15:30'),
    'min_percentage' => (int) env('ATTENDANCE_MIN_PERCENTAGE', 75),
    'working_days' => (int) env('ATTENDANCE_WORKING_DAYS', 22),
    'statuses' => [
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'alfa' => 'Tidak Hadir (Alfa)',
        'terlambat' => 'Terlambat',
    ],
];