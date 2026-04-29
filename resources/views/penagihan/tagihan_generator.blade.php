{{-- resources/views/penagihan/tagihan_generator.blade.php --}}
@extends('layouts.app')

@section('title', 'Penagihan Bills')

@section('topbar-nav')
    <a href="{{ route('penagihan.index') }}" class="tb-link">Dashboard</a>
    <a href="{{ route('penagihan.generator') }}" class="tb-link active">Tagihan Generator</a>
@endsection

@section('subbar-actions')
    <button onclick="openModalGenerate()" class="btn-primary" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;border:none;margin-right:6px;">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="1" x2="7" y2="13"></line><line x1="1" y1="7" x2="13" y2="7"></line></svg>
        Generate Tagihan Perusahaan
    </button>
    <button onclick="openModalMandiri()" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;border:none;margin-right:6px;background:#1D4ED8;color:#fff;font-weight:600;padding:8px 14px;border-radius:8px;font-size:13px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        Generate Tagihan Mandiri
    </button>
@endsection

@section('page-title', 'Daftar Tagihan (Bills)')

@section('content')
<div x-data="{ tab: window.location.hash === '#tab-mandiri' && {{ session()->has('_flash') ? 'true' : 'false' }} ? 'mandiri' : 'gabungan' }" style="margin:20px;">
    @if(session('success'))
        <div style="background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tab Bar --}}
    <div style="display:flex;gap:4px;border-bottom:2px solid #E5E7EB;margin-bottom:0;">
        <button @click="tab='gabungan'" type="button"
            :style="tab==='gabungan' ? 'border-bottom:2px solid #059669;color:#059669;' : 'border-bottom:2px solid transparent;color:#6B7280;'"
            style="padding:10px 20px;font-size:13px;font-weight:700;border:none;background:transparent;cursor:pointer;display:inline-flex;align-items:center;gap:6px;margin-bottom:-2px;transition:.15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            Tagihan Perusahaan (Gabungan)
            <span style="font-size:10px;font-weight:700;padding:1px 8px;border-radius:20px;background:#DCFCE7;color:#15803D;">{{ $billsGabungan->total() }}</span>
        </button>
        <button @click="tab='mandiri'" type="button"
            :style="tab==='mandiri' ? 'border-bottom:2px solid #1D4ED8;color:#1D4ED8;' : 'border-bottom:2px solid transparent;color:#6B7280;'"
            style="padding:10px 20px;font-size:13px;font-weight:700;border:none;background:transparent;cursor:pointer;display:inline-flex;align-items:center;gap:6px;margin-bottom:-2px;transition:.15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            Tagihan Mandiri (Perorangan)
            <span style="font-size:10px;font-weight:700;padding:1px 8px;border-radius:20px;background:#DBEAFE;color:#1D4ED8;">{{ $billsMandiri->total() }}</span>
        </button>
    </div>

    {{-- ══ Tab Perusahaan / Gabungan ══ --}}
    <div x-show="tab==='gabungan'" x-transition>
        <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-top:none;border-radius:0 0 10px 10px;padding:10px 18px;font-size:12px;color:#065F46;margin-bottom:12px;">
            <strong>Tagihan Perusahaan:</strong> Berisi potongan gaji — simpanan wajib/pokok/sukarela + cicilan pinjaman potong gaji. Diproses melalui payroll perusahaan.
        </div>
        <div class="data-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="td-check"><input type="checkbox" id="checkAllGabungan" onclick="document.querySelectorAll('.row-check-g').forEach(c=>c.checked=this.checked)"></th>
                        <th>Periode</th>
                        <th>Tanggal Generate</th>
                        <th>Status</th>
                        <th>Total Potongan Gaji</th>
                        <th style="text-align:right;padding-right:20px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billsGabungan as $item)
                    <tr>
                        <td class="td-check" onclick="event.stopPropagation()"><input type="checkbox" class="row-check-g" value="{{ $item->id }}"></td>
                        <td><div class="td-name"><span style="font-weight:600;color:#111827;">{{ $item->periode }}</span></div></td>
                        <td style="color:#4B5563;">{{ \Carbon\Carbon::parse($item->tgl_generate)->format('d M Y') }}</td>
                        <td>
                            @if($item->status == 'Draft')
                                <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;background:#F3F4F6;color:#4B5563;font-weight:600;">Draft</span>
                            @elseif($item->status == 'Partial')
                                <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;background:#FEF3C7;color:#B45309;font-weight:600;">Partial</span>
                            @else
                                <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;background:#DEF7EC;color:#03543F;font-weight:600;">Paid</span>
                            @endif
                        </td>
                        <td style="font-weight:800;color:#111827;">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                        <td style="text-align:right;padding-right:20px;">
                            <button onclick="window.location.href='{{ route('penagihan.show', $item->id) }}'" style="border:1px solid #D1D5DB;background:#fff;color:#374151;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;">Detail Report</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px 16px;color:#6B7280;">Belum ada Tagihan Perusahaan yang ter-generate.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding:15px 20px;">{{ $billsGabungan->appends(['mandiri' => request('mandiri')])->links('pagination::tailwind') }}</div>
        </div>
    </div>

    {{-- ══ Tab Mandiri ══ --}}
    <div x-show="tab==='mandiri'" x-transition style="display:none;">
        <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-top:none;border-radius:0 0 10px 10px;padding:10px 18px;font-size:12px;color:#1E40AF;margin-bottom:12px;">
            <strong>Tagihan Mandiri:</strong> Berisi daftar cicilan pinjaman yang dibayar langsung oleh anggota (tidak dipotong dari gaji). Digunakan sebagai reminder/referensi pembayaran.
        </div>
        <div class="data-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="td-check"><input type="checkbox" id="checkAllMandiri" onclick="document.querySelectorAll('.row-check-m').forEach(c=>c.checked=this.checked)"></th>
                        <th>Periode</th>
                        <th>Tanggal Generate</th>
                        <th>Status</th>
                        <th>Total Cicilan Mandiri</th>
                        <th style="text-align:right;padding-right:20px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billsMandiri as $item)
                    <tr>
                        <td class="td-check" onclick="event.stopPropagation()"><input type="checkbox" class="row-check-m" value="{{ $item->id }}"></td>
                        <td>
                            <div class="td-name">
                                <span style="font-weight:600;color:#111827;">{{ $item->periode }}</span>
                                <span style="display:inline-block;padding:1px 7px;border-radius:20px;font-size:10px;background:#DBEAFE;color:#1D4ED8;font-weight:700;margin-left:6px;">MANDIRI</span>
                            </div>
                        </td>
                        <td style="color:#4B5563;">{{ \Carbon\Carbon::parse($item->tgl_generate)->format('d M Y') }}</td>
                        <td>
                            @if($item->status == 'Draft')
                                <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;background:#F3F4F6;color:#4B5563;font-weight:600;">Draft</span>
                            @elseif($item->status == 'Partial')
                                <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;background:#FEF3C7;color:#B45309;font-weight:600;">Partial</span>
                            @else
                                <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;background:#DEF7EC;color:#03543F;font-weight:600;">Paid</span>
                            @endif
                        </td>
                        <td style="font-weight:800;color:#1D4ED8;">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                        <td style="text-align:right;padding-right:20px;">
                            <button onclick="window.location.href='{{ route('penagihan.show', $item->id) }}'" style="border:1px solid #BFDBFE;background:#EFF6FF;color:#1D4ED8;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Detail Report</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px 16px;color:#6B7280;">Belum ada Tagihan Mandiri yang ter-generate.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding:15px 20px;">{{ $billsMandiri->appends(['gabungan' => request('gabungan')])->links('pagination::tailwind') }}</div>
        </div>
    </div>

</div>

{{-- Modal Generate Perusahaan --}}
<div id="modalGenerate" class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm">
    <form method="POST" action="{{ route('penagihan.storeGenerate') }}" class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden mx-4 flex flex-col">
        @csrf
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-[17px] font-bold text-gray-800">Generate Tagihan Perusahaan</h3>
                <p class="text-xs text-gray-400 mt-0.5">Simpanan + Pinjaman Potong Gaji</p>
            </div>
            <button type="button" onclick="closeModalGenerate()" class="text-gray-400 hover:text-gray-600">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-xs text-gray-500 mb-4 bg-green-50 p-3 rounded-lg border border-green-100">
                Sistem akan otomatis menghitung seluruh tagihan Simpanan dan angsuran Pinjaman <strong>Potong Gaji</strong> untuk setiap anggota aktif.
            </p>
            <div class="space-y-4">
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Periode (Bulan & Tahun)</label>
                    <input type="month" name="periode" required value="{{ date('Y-m') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Tagihan Dibuat</label>
                    <input type="date" name="tanggal_generate" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" onclick="closeModalGenerate()" class="px-5 py-2.5 text-[13px] font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">Batal</button>
            <button type="submit" class="px-5 py-2.5 text-[13px] font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 cursor-pointer border border-transparent">Generate Data</button>
        </div>
    </form>
</div>

{{-- Modal Generate Mandiri --}}
<div id="modalMandiri" class="fixed inset-0 z-[999] hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm">
    <form method="POST" action="{{ route('penagihan.storeGenerateMandiri') }}" class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden mx-4 flex flex-col">
        @csrf
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-[17px] font-bold text-blue-800">Generate Tagihan Mandiri</h3>
                <p class="text-xs text-blue-400 mt-0.5">Pinjaman Bayar Sendiri (Perorangan)</p>
            </div>
            <button type="button" onclick="closeModalMandiri()" class="text-gray-400 hover:text-gray-600">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-xs text-blue-700 mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                Sistem akan membuat daftar anggota yang memiliki pinjaman aktif dengan metode pembayaran <strong>Mandiri</strong>. Digunakan sebagai referensi penagihan langsung ke anggota.
            </p>
            <div class="space-y-4">
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Periode (Bulan & Tahun)</label>
                    <input type="month" name="periode" required value="{{ date('Y-m') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 mb-1.5">Tanggal Tagihan Dibuat</label>
                    <input type="date" name="tanggal_generate" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-[14px] text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-blue-50 border-t border-blue-100 flex justify-end gap-3">
            <button type="button" onclick="closeModalMandiri()" class="px-5 py-2.5 text-[13px] font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">Batal</button>
            <button type="submit" class="px-5 py-2.5 text-[13px] font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 cursor-pointer border border-transparent flex items-center gap-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                Generate Tagihan Mandiri
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    const modalGenerate = document.getElementById('modalGenerate');
    const modalMandiri  = document.getElementById('modalMandiri');

    function openModalGenerate()  { modalGenerate.classList.remove('hidden'); modalGenerate.classList.add('flex'); }
    function closeModalGenerate() { modalGenerate.classList.add('hidden'); modalGenerate.classList.remove('flex'); }
    function openModalMandiri()   { modalMandiri.classList.remove('hidden'); modalMandiri.classList.add('flex'); }
    function closeModalMandiri()  { modalMandiri.classList.add('hidden'); modalMandiri.classList.remove('flex'); }
</script>
@endpush
