{{-- resources/views/simpanan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Simpanan Anggota')

{{-- ── Topbar nav ── --}}
@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link active">Simpanan Anggota</a>
    <a href="{{ route('simpanan.transaksi') }}" class="tb-link">Transaksi</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
    <a href="{{ route('simpanan.tagihangenerator') }}" class="tb-link">Tagih Simpanan</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('simpanan.index') }}" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Kembali
    </a>
@endsection

@section('page-title', 'Detail Simpanan: ' . ($master->anggota->nama_anggota ?? ''))

@section('content')
<div class="px-6 py-4 space-y-6" x-data="{ 
    activeTab: 'setting',
    editSimpanan: 'Wajib',
    editNominal: {{ $master->simpanan_wajib }},
    editDate: '{{ $master->tanggal_mulai }}',
    setEdit(jenis, nominal) {
        if(jenis === 'Pokok') return; // no action for pokok typically, but allowing if needed
        this.editSimpanan = jenis;
        this.editNominal = nominal;
    }
}">
    
    <style>
        .stats-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .stat-card-dark {
            background:#0B1727; border-radius:12px; padding:24px; color:#fff; position:relative; overflow:hidden;
        }
        .stat-card-light {
            background:#fff; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border:1px solid #f1f5f9;
        }
        .icon-box {
            width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;
        }
        .icon-pokok { background: #F3F4F6; color: #6B7280; }
        .icon-wajib { background: #EFF6FF; color: #3B82F6; }
        .icon-sukarela { background: #FEF3C7; color: #D97706; }
        
        .tabs-header {
            display: flex; gap: 30px; border-bottom: 1px solid #E5E7EB; margin-bottom: 20px;
        }
        .tab-btn {
            padding: 12px 0; font-weight: 600; font-size: 14px; color: #6B7280; border-bottom: 2px solid transparent; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;
        }
        .tab-btn:hover { color: #111827; }
        .tab-btn.active { color: #2563EB; border-bottom-color: #2563EB; }

        .form-input { w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 }
    </style>

    {{-- 1. Top Cards --}}
    <div class="stats-grid-4">
        <div class="stat-card-dark">
            <div class="text-gray-400 text-[10px] font-bold tracking-wider mb-2 uppercase">TOTAL KESELURUHAN</div>
            <div class="text-2xl font-bold text-white mb-2">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</div>
            <div class="text-xs text-green-400 font-medium">
                <svg class="inline w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Terakumulasi
            </div>
        </div>
        
        <div class="stat-card-light">
            <div class="icon-box icon-pokok">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div class="text-gray-400 text-[10px] font-bold tracking-wider mb-1 uppercase">SIMPANAN POKOK</div>
            <div class="text-xl font-bold text-gray-800 mb-1">Rp {{ number_format($master->simpanan_pokok, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-500 font-medium">Sekali bayar di awal</div>
        </div>

        <div class="stat-card-light">
            <div class="icon-box icon-wajib">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="text-gray-400 text-[10px] font-bold tracking-wider mb-1 uppercase">SIMPANAN WAJIB</div>
            <div class="text-xl font-bold text-gray-800 mb-1">Rp {{ number_format($master->simpanan_wajib, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-500 font-medium">Per bulan (Rutin)</div>
        </div>

        <div class="stat-card-light">
            <div class="icon-box icon-sukarela">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div class="text-gray-400 text-[10px] font-bold tracking-wider mb-1 uppercase">SIMPANAN SUKARELA</div>
            <div class="text-xl font-bold text-gray-800 mb-1">Rp {{ number_format($master->simpanan_sukarela, 0, ',', '.') }}</div>
            <div class="text-[11px] text-gray-500 font-medium">Kontribusi fleksibel</div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="tabs-header">
        <div @click="activeTab = 'setting'" :class="{'active': activeTab === 'setting'}" class="tab-btn">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
            Setting Simpanan
        </div>
        <div @click="activeTab = 'riwayat'" :class="{'active': activeTab === 'riwayat'}" class="tab-btn">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat Transaksi
        </div>
    </div>

    {{-- Content: Setting Simpanan --}}
    <div x-show="activeTab === 'setting'" x-transition class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Tabel Konfigurasi -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-[15px] text-gray-800">Konfigurasi Simpanan Rutin</h3>
                    <p class="text-[12px] text-gray-500 mt-1">Daftar kewajiban dan iuran rutin anggota.</p>
                </div>
            </div>
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[11px] uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">JENIS SIMPANAN</th>
                        <th class="px-6 py-3">NOMINAL PER BULAN</th>
                        <th class="px-6 py-3">TANGGAL MULAI</th>
                        <th class="px-6 py-3">STATUS</th>
                        <th class="px-6 py-3 text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Pokok -->
                    <tr class="hover:bg-gray-50/50 transition-colors cursor-default">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="icon-box icon-pokok m-0 w-8 h-8"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                            <span class="font-bold text-gray-800">Pokok</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Rp {{ number_format($master->simpanan_pokok, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($master->tanggal_mulai)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">
                            <span style="font-size:11px; padding:2px 8px; border-radius:12px; background:#DEF7EC; color:#03543F; font-weight:600;">Aktif</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs text-gray-400 font-medium italic">No Action</span>
                        </td>
                    </tr>
                    <!-- Wajib -->
                    <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="setEdit('Wajib', {{ $master->simpanan_wajib }})">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="icon-box icon-wajib m-0 w-8 h-8"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                            <span class="font-bold text-gray-800">Wajib</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Rp {{ number_format($master->simpanan_wajib, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($master->tanggal_mulai)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">
                            <span style="font-size:11px; padding:2px 8px; border-radius:12px; background:#DEF7EC; color:#03543F; font-weight:600;">Aktif</span>
                        </td>
                        <td class="px-6 py-4 flex justify-center">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg shadow-sm transition-colors">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Sukarela -->
                    <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="setEdit('Sukarela', {{ $master->simpanan_sukarela }})">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="icon-box icon-sukarela m-0 w-8 h-8"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                            <span class="font-bold text-gray-800">Sukarela</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Rp {{ number_format($master->simpanan_sukarela, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($master->tanggal_mulai)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">
                            <span style="font-size:11px; padding:2px 8px; border-radius:12px; background:#DEF7EC; color:#03543F; font-weight:600;">Aktif</span>
                        </td>
                        <td class="px-6 py-4 flex justify-center">
                            <button class="border border-gray-200 hover:bg-gray-50 text-gray-500 p-2 rounded-lg transition-colors">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Form Edit -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden self-start">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="bg-blue-50 text-blue-600 p-2 rounded-lg">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <h3 class="font-bold text-[15px] text-gray-800">Edit Simpanan</h3>
            </div>
            
            <form method="POST" action="{{ route('simpanan.update', $master->id) }}" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase tracking-wide">JENIS SIMPANAN</label>
                    <input type="text" name="jenis_simpanan" x-model="editSimpanan" readonly class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-600 focus:outline-none">
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase tracking-wide">NOMINAL BARU (RP)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400 text-sm">Rp</span>
                        <input type="number" name="nominal_baru" x-model="editNominal" required class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-shadow">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5 italic">Nominal ini akan ditagihkan secara rutin setiap bulan.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase tracking-wide">TANGGAL MULAI BERLAKU</label>
                    <div class="relative">
                        <input type="date" name="tanggal_mulai" x-model="editDate" required class="w-full border border-gray-300 rounded-lg pl-3 pr-9 py-2 text-sm text-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition-shadow">
                        <svg class="absolute right-3 top-2.5 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg text-[13px] transition-colors shadow-sm mb-3">
                    Simpan Perubahan
                </button>
                <div class="text-center">
                    <button type="button" @click="editSimpanan='Wajib'; editNominal={{$master->simpanan_wajib}}" class="text-gray-500 hover:text-gray-700 text-xs font-semibold">Batal</button>
                </div>
            </form>
        </div>

    </div>

    {{-- Content: Riwayat Transaksi --}}
    <div x-show="activeTab === 'riwayat'" x-cloak x-transition>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-[15px] text-gray-800">Riwayat Transaksi Anggota</h3>
            </div>
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[11px] uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">TANGGAL</th>
                        <th class="px-6 py-4">PERIODE</th>
                        <th class="px-6 py-4">JENIS</th>
                        <th class="px-6 py-4">KETERANGAN</th>
                        <th class="px-6 py-4 text-right">NOMINAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayatTransaksi as $trx)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $trx->periode }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $jClass = 'bg-blue-50 text-blue-600';
                                    $nama = strtolower($trx->jenisSimpanan->nama ?? '');
                                    if(str_contains($nama, 'wajib')) $jClass = 'bg-green-50 text-green-600';
                                    elseif(str_contains($nama, 'pokok')) $jClass = 'bg-orange-50 text-orange-600';
                                @endphp
                                <span style="font-size:10px; padding:3px 8px; border-radius:12px; font-weight:700;" class="{{ $jClass }}">
                                    {{ strtoupper($trx->jenisSimpanan->nama ?? '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $trx->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-800">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">Belum ada riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
