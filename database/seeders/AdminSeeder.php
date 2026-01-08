<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@perpustakaan.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'siswa@perpustakaan.id'],
            [
                'name' => 'Siswa Demo',
                'password' => bcrypt('siswa123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );
    }
}
