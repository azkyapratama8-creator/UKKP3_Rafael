<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun admin menggunakan firstOrCreate() untuk keamanan
        // Kredensial ini hanya untuk pengembangan lokal dan harus diganti sebelum digunakan sungguhan
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('Admin12345!'), // Password wajib di-hash
                'role' => 'admin',
            ]
        );
    }
}
