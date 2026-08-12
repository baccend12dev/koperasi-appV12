<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Modul Anggota
            ['name' => 'anggota.view', 'label' => 'Lihat Data Anggota', 'module' => 'Anggota', 'description' => 'Dapat melihat daftar dan detail anggota'],
            ['name' => 'anggota.create', 'label' => 'Tambah Anggota Baru', 'module' => 'Anggota', 'description' => 'Dapat menambah anggota baru'],
            ['name' => 'anggota.edit', 'label' => 'Edit Data Anggota', 'module' => 'Anggota', 'description' => 'Dapat mengubah informasi profil anggota'],
            ['name' => 'anggota.delete', 'label' => 'Hapus Anggota', 'module' => 'Anggota', 'description' => 'Dapat menghapus data anggota'],

            // Modul Simpanan
            ['name' => 'simpanan.view', 'label' => 'Lihat Data Simpanan', 'module' => 'Simpanan', 'description' => 'Dapat melihat saldo dan transaksi simpanan'],
            ['name' => 'simpanan.create', 'label' => 'Setor / Tambah Simpanan', 'module' => 'Simpanan', 'description' => 'Dapat menginput setoran simpanan'],
            ['name' => 'simpanan.withdraw', 'label' => 'Pengajuan Penarikan Simpanan', 'module' => 'Simpanan', 'description' => 'Dapat menginput penarikan simpanan'],
            ['name' => 'simpanan.export', 'label' => 'Export Data Simpanan', 'module' => 'Simpanan', 'description' => 'Dapat mengeksport laporan simpanan ke Excel'],

            // Modul Pinjaman
            ['name' => 'pinjaman.view', 'label' => 'Lihat Data Pinjaman', 'module' => 'Pinjaman', 'description' => 'Dapat melihat daftar pengajuan dan pinjaman aktif'],
            ['name' => 'pinjaman.create', 'label' => 'Buat Pengajuan Pinjaman', 'module' => 'Pinjaman', 'description' => 'Dapat menginput pengajuan pinjaman baru'],
            ['name' => 'pinjaman.approve', 'label' => 'Persetujuan / Approval Pinjaman', 'module' => 'Pinjaman', 'description' => 'Dapat menyetujui atau menolak pengajuan pinjaman'],
            ['name' => 'pinjaman.disburse', 'label' => 'Pencairan Pinjaman', 'module' => 'Pinjaman', 'description' => 'Dapat memproses pencairan dana pinjaman'],
            ['name' => 'pinjaman.angsuran', 'label' => 'Kelola Angsuran Pinjaman', 'module' => 'Pinjaman', 'description' => 'Dapat mencatat dan membayar angsuran pinjaman'],
            ['name' => 'pinjaman.simulasi', 'label' => 'Akses Simulasi Pinjaman & Tarik', 'module' => 'Pinjaman', 'description' => 'Dapat menggunakan kalkulator simulasi'],

            // Modul Persetujuan & Pencairan
            ['name' => 'persetujuan.view', 'label' => 'Akses Modul Persetujuan', 'module' => 'Persetujuan', 'description' => 'Dapat mengakses halaman persetujuan pengajuan'],
            ['name' => 'pencairan.view', 'label' => 'Akses Modul Pembayaran / Pencairan', 'module' => 'Pencairan', 'description' => 'Dapat mengakses halaman pencairan dana & kasir'],

            // Modul Penagihan
            ['name' => 'penagihan.view', 'label' => 'Lihat Tagihan & Invoice', 'module' => 'Penagihan', 'description' => 'Dapat melihat data penagihan bulanan'],
            ['name' => 'penagihan.generate', 'label' => 'Generate Tagihan Generator', 'module' => 'Penagihan', 'description' => 'Dapat membuat draf potongan gaji bulanan'],
            ['name' => 'penagihan.invoice', 'label' => 'Generate Invoice Koperasi', 'module' => 'Penagihan', 'description' => 'Dapat mencetak dan mengirim invoice'],

            // Modul Laporan
            ['name' => 'laporan.view', 'label' => 'Lihat Laporan Koperasi', 'module' => 'Laporan', 'description' => 'Dapat mengakses dashboard & laporan analitik'],
            ['name' => 'laporan.export', 'label' => 'Export Laporan Koperasi', 'module' => 'Laporan', 'description' => 'Dapat mengunduh file export laporan'],

            // Modul Pengurus & Sistem
            ['name' => 'pengurus.users', 'label' => 'Kelola Data User', 'module' => 'Pengurus', 'description' => 'Dapat melihat, membuat, dan mengubah data user'],
            ['name' => 'pengurus.roles', 'label' => 'Kelola Role Pengguna', 'module' => 'Pengurus', 'description' => 'Dapat mengatur role dan hak aksesnya'],
            ['name' => 'pengurus.permissions', 'label' => 'Kelola Master Permission', 'module' => 'Pengurus', 'description' => 'Dapat mengatur master modul & permission'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm['name']], $perm);
        }

        // Auto assign all permissions to 'Admin' or existing roles if present
        $allPermissions = Permission::all();

        $adminRole = Role::whereIn('name', ['Admin', 'Super Admin'])->first();
        if ($adminRole) {
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }

        $pengurusRole = Role::where('name', 'Pengurus')->first();
        if ($pengurusRole) {
            $pengurusPermissions = Permission::whereIn('module', ['Anggota', 'Simpanan', 'Pinjaman', 'Persetujuan', 'Pencairan', 'Penagihan', 'Laporan'])->get();
            $pengurusRole->permissions()->sync($pengurusPermissions->pluck('id'));
        }
    }
}
