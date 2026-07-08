<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinjamanSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_pinjaman_sorting(): void
    {
        // 1. Create a user and authenticate
        $user = User::factory()->create();

        // 2. Create 3 Anggota (Members)
        $anggotaA = Anggota::create([
            'nik' => '111',
            'nama_anggota' => 'Anggota A',
        ]);

        $anggotaB = Anggota::create([
            'nik' => '222',
            'nama_anggota' => 'Anggota B',
        ]);

        $anggotaC = Anggota::create([
            'nik' => '333',
            'nama_anggota' => 'Anggota C',
        ]);

        // 3. Create Pinjamans for Anggota A
        // Total pokok: 50,000,000, Total count: 1, Total sisa: 10,000,000
        Pinjaman::create([
            'user_id' => $anggotaA->id,
            'jumlah_pinjaman' => 50000000,
            'total_pinjaman' => 55000000,
            'sisa_pinjaman' => 10000000,
            'tenor' => 12,
            'status' => 'berjalan',
            'cicilan_per_bulan' => 5000000,
        ]);

        // Create Pinjamans for Anggota B
        // Total pokok: 20,000,000 + 10,000,000 = 30,000,000, Total count: 2, Total sisa: 25,000,000
        Pinjaman::create([
            'user_id' => $anggotaB->id,
            'jumlah_pinjaman' => 20000000,
            'total_pinjaman' => 22000000,
            'sisa_pinjaman' => 15000000,
            'tenor' => 12,
            'status' => 'berjalan',
            'cicilan_per_bulan' => 2000000,
        ]);
        Pinjaman::create([
            'user_id' => $anggotaB->id,
            'jumlah_pinjaman' => 10000000,
            'total_pinjaman' => 11000000,
            'sisa_pinjaman' => 10000000,
            'tenor' => 12,
            'status' => 'berjalan',
            'cicilan_per_bulan' => 1000000,
        ]);

        // Create Pinjamans for Anggota C
        // Total pokok: 10,000,000, Total count: 1, Total sisa: 9,000,000
        Pinjaman::create([
            'user_id' => $anggotaC->id,
            'jumlah_pinjaman' => 10000000,
            'total_pinjaman' => 11000000,
            'sisa_pinjaman' => 9000000,
            'tenor' => 12,
            'status' => 'berjalan',
            'cicilan_per_bulan' => 1000000,
        ]);

        // 4. Test Sort By 'jumlah_pinjaman_tertinggi'
        // Expected order: A (50M), B (30M), C (10M)
        $response = $this->actingAs($user)->get(route('pinjaman.index', ['sort' => 'jumlah_pinjaman_tertinggi']));
        $response->assertStatus(200);
        $anggotaList = $response->viewData('anggotaList');
        $this->assertEquals($anggotaA->id, $anggotaList[0]->id);
        $this->assertEquals($anggotaB->id, $anggotaList[1]->id);
        $this->assertEquals($anggotaC->id, $anggotaList[2]->id);

        // 5. Test Sort By 'jumlah_pinjaman_terbanyak' (count)
        // Expected order: B (2 loans), A (1 loan), C (1 loan)
        $response = $this->actingAs($user)->get(route('pinjaman.index', ['sort' => 'jumlah_pinjaman_terbanyak']));
        $response->assertStatus(200);
        $anggotaList = $response->viewData('anggotaList');
        $this->assertEquals($anggotaB->id, $anggotaList[0]->id);
        // A and C both have 1 loan, so their relative order depends on secondary sort/original DB insertion. We just assert B is first.
        $this->assertEquals(2, $anggotaList[0]->pinjaman->count());

        // 6. Test Sort By 'jumlah_sisa_terbanyak'
        // Expected order: B (25M sisa), A (10M sisa), C (9M sisa)
        $response = $this->actingAs($user)->get(route('pinjaman.index', ['sort' => 'jumlah_sisa_terbanyak']));
        $response->assertStatus(200);
        $anggotaList = $response->viewData('anggotaList');
        $this->assertEquals($anggotaB->id, $anggotaList[0]->id);
        $this->assertEquals($anggotaA->id, $anggotaList[1]->id);
        $this->assertEquals($anggotaC->id, $anggotaList[2]->id);
    }
}
