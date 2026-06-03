<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin'], ['description' => 'Administrator dengan akses penuh']);
        $pengurusRole = Role::firstOrCreate(['name' => 'Pengurus'], ['description' => 'Pengurus Koperasi']);
        $anggotaRole = Role::firstOrCreate(['name' => 'Anggota'], ['description' => 'Anggota Koperasi']);

        // 2. Seed Users
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@koperasi.com'],
            [
                'nik' => '1001',
                'name' => 'Admin Koperasi',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]
        );

        // Pengurus
        User::firstOrCreate(
            ['email' => 'pengurus@koperasi.com'],
            [
                'nik' => '1002',
                'name' => 'Pengurus Koperasi',
                'password' => Hash::make('password'),
                'role_id' => $pengurusRole->id,
                'status' => 'active',
            ]
        );

        // Anggota
        User::firstOrCreate(
            ['email' => 'anggota@koperasi.com'],
            [
                'nik' => '1003',
                'name' => 'Anggota Koperasi',
                'password' => Hash::make('password'),
                'role_id' => $anggotaRole->id,
                'status' => 'active',
            ]
        );

        // Inactive user
        User::firstOrCreate(
            ['email' => 'inactive@koperasi.com'],
            [
                'nik' => '1004',
                'name' => 'Inactive Anggota',
                'password' => Hash::make('password'),
                'role_id' => $anggotaRole->id,
                'status' => 'inactive',
            ]
        );
    }
}
