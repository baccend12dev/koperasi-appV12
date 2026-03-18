<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departemen;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Departemen::create([
            'nama' => 'IT',
            'kode' => 'IT',
            'deskripsi' => 'Departemen IT',
        ]);
        Departemen::create([
            'nama' => 'HR',
            'kode' => 'HR',
            'deskripsi' => 'Departemen HR',
        ]);
        Departemen::create([
            'nama' => 'Finance',
            'kode' => 'FIN',
            'deskripsi' => 'Departemen Finance',
        ]);
        Departemen::create([
            'nama' => 'Sales',
            'kode' => 'SAL',
            'deskripsi' => 'Departemen Sales',
        ]);
        Departemen::create([
            'nama' => 'Marketing',
            'kode' => 'MKT',
            'deskripsi' => 'Departemen Marketing',
        ]);
    }
}