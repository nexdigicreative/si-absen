<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'name' => 'Administrator',
            'email' => 'admin@sman1bdg.sch.id',
            'role' => 'admin',
            'status' => true,
        ]);

        // Kepala Sekolah
        User::create([
            'username' => 'kepsek',
            'password' => Hash::make('kepsek123'),
            'name' => 'Dr. H. Wahyu Santoso, M.Pd',
            'email' => 'kepala@sman1bdg.sch.id',
            'role' => 'kepala_sekolah',
            'status' => true,
        ]);

        // Guru accounts (created with TeacherSeeder)
        $guruData = [
            ['siti.rahayu', 'Dra. Hj. Siti Rahayu, M.Pd', 'siti.rahayu@sman1bdg.sch.id'],
            ['budi.santoso', 'Budi Santoso, S.Pd', 'budi.santoso@sman1bdg.sch.id'],
            ['indah.permata', 'Indah Permata, S.Si', 'indah.permata@sman1bdg.sch.id'],
            ['ahmad.firdaus', 'Ahmad Firdaus, S.Sos', 'ahmad.firdaus@sman1bdg.sch.id'],
            ['dewi.kusuma', 'Dewi Kusuma, S.Pd', 'dewi.kusuma@sman1bdg.sch.id'],
        ];

        foreach ($guruData as [$username, $name, $email]) {
            User::create([
                'username' => $username,
                'password' => Hash::make('guru123'),
                'name' => $name,
                'email' => $email,
                'role' => 'guru',
                'status' => true,
            ]);
        }
    }
}