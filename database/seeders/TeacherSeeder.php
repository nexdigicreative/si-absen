<?php

namespace Database\Seeders;

use App\Models\{Teacher, User};
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['199001012020121001', 'Dra. Hj. Siti Rahayu, M.Pd', 'Bahasa Indonesia', '081298765432', 'siti.rahayu'],
            ['198503052019031002', 'Budi Santoso, S.Pd', 'Matematika', '081298765433', 'budi.santoso'],
            ['199208152021122003', 'Indah Permata, S.Si', 'Fisika', '081298765434', 'indah.permata'],
            ['198712302018031004', 'Ahmad Firdaus, S.Sos', 'Ekonomi', '081298765435', 'ahmad.firdaus'],
            ['199504122022121005', 'Dewi Kusuma, S.Pd', 'Bahasa Inggris', '081298765436', 'dewi.kusuma'],
        ];

        foreach ($teachers as [$nip, $name, $subject, $phone, $username]) {
            $user = User::where('username', $username)->first();
            Teacher::create([
                'nip' => $nip,
                'name' => $name,
                'subject' => $subject,
                'phone' => $phone,
                'email' => $username . '@sman1bdg.sch.id',
                'user_id' => $user->id,
                'status' => true,
            ]);
        }
    }
}