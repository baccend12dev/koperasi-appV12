{{-- resources/views/Simpanan/tarik_saldo.blade.php --}}
@extends('layouts.app')

@section('title', 'Tarik Simpanan')

@section('topbar-nav')
    <a href="{{ route('simpanan.index') }}" class="tb-link active">Simpanan Anggota</a>
    <a href="{{ route('simpanan.transaksi') }}" class="tb-link">Transaksi</a>
    <a href="{{ route('laporan.index') }}" class="tb-link">Laporan</a>
@endsection

@section('subbar-actions')
    <a href="{{ route('simpanan.show', $master->id) }}" class="btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;margin-right:4px;">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Batal & Kembali
    </a>
@endsection

@section('page-title', 'Pengajuan Penarikan Simpanan')

@section('content')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 30px;
        max-width: 600px;
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-2);
        margin-bottom: 8px;
    }
    .form-control {
        width: 100%;
        height: 42px;
        padding: 0 12px;
        font-size: 14px;
        color: var(--text-1);
        background: var(--bg);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-md);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(92, 79, 138, 0.12);
        background: var(--surface);
    }
    .form-control[readonly] {
        background: var(--bg);
        color: var(--text-2);
        cursor: not-allowed;
    }
    textarea.form-control {
        height: auto;
        min-height: 100px;
        padding-top: 10px;
        resize: vertical;
    }
    .btn-submit {
        display: flex;
        justify-content: center;
        width: 100%;
        height: 44px;
        background: var(--danger, #E02424);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: opacity .15s;
    }
    .btn-submit:hover { opacity: 0.9; }
    
    .info-box {
        background: #FDFDEA;
        border: 1px solid #FDF6B2;
        padding: 16px;
        border-radius: var(--radius-md);
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .info-box.danger {
        background: #FDF2F2;
        border-color: #FBD5D5;
        color: #9B1C1C;
    }

    /* ── Refinancing Section (Loan Settlement) ── */
    .refinance-box {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 20px;
        margin-top: 24px;
    }
    .ref-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .ref-title {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* Toggle Switch */
    .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider-sw { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #D1D5DB; transition: .3s; border-radius: 34px; }
    .slider-sw:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
    input:checked + .slider-sw { background-color: #10B981; }
    input:checked + .slider-sw:before { transform: translateX(20px); }
    .sw-wrap { display: flex; align-items: center; gap: 12px; font-size: 13px; font-weight: 600; color: #111827; }

    /* Loan List */
    #refinance-list { display: none; flex-direction: column; gap: 10px; margin-top: 15px; }
    #refinance-list.show { display: flex; }
    .ref-item {
        display: flex; align-items: center; background: #fff; border: 1.5px solid #E5E7EB;
        padding: 12px 14px; border-radius: 8px; cursor: pointer; transition: 0.2s; gap: 12px;
    }
    .ref-item:hover { border-color: #D1D5DB; }
    .ref-item.selected { border-color: #10B981; background: #F0FDF4; }

    .cb-custom {
        width: 18px; height: 18px; border: 2px solid #D1D5DB; border-radius: 4px;
        display: flex; align-items: center; justify-content: center; transition: 0.2s;
        flex-shrink: 0;
    }
    .ref-item.selected .cb-custom { background: #10B981; border-color: #10B981; }
    .cb-custom svg { width: 10px; height: 10px; color: #fff; opacity: 0; }
    .ref-item.selected .cb-custom svg { opacity: 1; }

    .ref-info { flex: 1; display: grid; grid-template-columns: 1.5fr 1fr 1fr; align-items: center; gap: 10px; }
    .ref-name { font-size: 12px; font-weight: 700; color: #111827; }
    .ref-id { font-size: 10px; color: #6B7280; margin-top: 1px; }
    .ref-val { font-size: 12px; font-weight: 700; color: #111827; }
    .ref-sub { font-size: 10px; color: #6B7280; margin-top: 1px; }

    /* Summary Bar */
    .summary-box {
        margin-top: 25px;
        padding: 20px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 12px;
    }
    .sum-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .sum-row:last-child { margin-bottom: 0; padding-top: 10px; border-top: 1px solid #BFDBFE; }
    .sum-label { font-size: 12px; font-weight: 600; color: #1E40AF; }
    .sum-value { font-size: 14px; font-weight: 700; color: #1E3A8A; }
    .sum-value.danger { color: #DC2626; }
    .sum-value.success { color: #059669; }
</style>

<div class="form-card">
    <div class="info-box">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D03801" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <div>
            <h4 style="margin: 0 0 4px 0; font-size: 14px; color: #D03801;">Informasi Saldo</h4>
            <p style="margin: 0; font-size: 13px; color: #8A2C0D; line-height: 1.4;">
                Total gabungan Simpanan <strong>{{ $anggota->nama_anggota }}</strong> adalah sebesar <strong>Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</strong>.<br>
                Penarikan tidak boleh melebihi total tersebut.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="info-box danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div style="font-size:13px; font-weight:500;">{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('simpanan.tarik.store') }}" method="POST">
        @csrf
        <input type="hidden" name="anggota_id" value="{{ $anggota->id }}">

        <div class="form-group">
            <label class="form-label">Anggota</label>
            <input type="text" class="form-control" value="{{ $anggota->nik }} - {{ $anggota->nama_anggota }}" readonly>
        </div>

        <div class="form-group">
            <label class="form-label">Nominal Penarikan (Rp)</label>
            <input type="number" name="nominal" class="form-control" placeholder="Contoh: 1000000" min="1" max="{{ $totalKeseluruhan }}" value="{{ old('nominal') }}" required>
            @error('nominal') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Alasan / Tujuan Pengajuan</label>
            <textarea name="alasan_pengajuan" class="form-control" placeholder="Contoh: Penarikan Tabungan" required>{{ old('alasan_pengajuan') }}</textarea>
            @error('alasan_pengajuan') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
        </div>

        {{-- REFINANCING SECTION --}}
        <div class="refinance-box">
            <div class="ref-header">
                <div class="ref-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="M17 3v2M7 3v2M3 11h18M4 7h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z"></path><path d="M9 15l2 2 4-4"></path></svg>
                    Opsi Pelunasan Hutang
                </div>
                <div class="sw-wrap">
                    <label class="switch">
                        <input type="checkbox" name="include_pelunasan" id="toggle-refinance" value="1" onchange="toggleRefinance(this)">
                        <span class="slider-sw"></span>
                    </label>
                    Gunakan sebagian penarikan untuk melunasi pinjaman
                </div>
            </div>
            
            <div id="refinance-list">
                <div style="text-align:center; padding:15px; color:#9CA3AF; font-size:12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:8px; opacity:0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><br>
                    Memuat data pinjaman aktif...
                </div>
            </div>
        </div>

        {{-- SUMMARY BOX --}}
        <div class="summary-box" id="summary-box" style="display: none;">
            <div class="sum-row">
                <span class="sum-label">Nominal Penarikan</span>
                <span class="sum-value" id="sum-nominal">Rp 0</span>
            </div>
            <div class="sum-row">
                <span class="sum-label">Total Pelunasan Pinjaman</span>
                <span class="sum-value danger" id="sum-pelunasan">Rp 0</span>
            </div>
            <div class="sum-row">
                <span class="sum-label">Sisa Dana Diterima (Net)</span>
                <span class="sum-value success" id="sum-net">Rp 0</span>
            </div>
        </div>

        <div id="pelunasan_inputs_container"></div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-submit">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Ajukan Penarikan
            </button>
        </div>
    </form>
</div>
<script>
    function rp(n) { return 'Rp ' + (Math.round(n) || 0).toLocaleString('id-ID'); }

    let activeLoans = [];
    let selectedLoans = [];
    const anggotaId = {{ $anggota->id }};
    const anggotaNik = '{{ $anggota->nik }}';

    document.addEventListener('DOMContentLoaded', function() {
        loadActiveLoans();
    });

    async function loadActiveLoans() {
        try {
            const res = await fetch(`{{ route('pinjaman.pengajuan.searchAnggota') }}?q=${encodeURIComponent(anggotaNik)}`);
            const data = await res.json();
            if (data.success) {
                activeLoans = data.data.pinjaman_berjalan || [];
                renderLoanList();
            }
        } catch (e) {
            console.error('Gagal memuat pinjaman:', e);
        }
    }

    function renderLoanList() {
        const list = document.getElementById('refinance-list');
        if (activeLoans.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding:15px; color:#9CA3AF; font-size:12px;">Tidak ada pinjaman berjalan yang bisa dilunasi.</div>';
            return;
        }

        list.innerHTML = '';
        activeLoans.forEach(loan => {
            const isSelected = selectedLoans.includes(loan.id);
            const item = document.createElement('div');
            item.className = `ref-item ${isSelected ? 'selected' : ''}`;
            item.onclick = () => toggleLoan(loan.id);
            item.innerHTML = `
                <div class="cb-custom">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div class="ref-info">
                    <div>
                        <div class="ref-name">${loan.jenis_pinjaman}</div>
                        <div class="ref-id">LN-${loan.id.toString().padStart(4, '0')}</div>
                    </div>
                    <div>
                        <div class="ref-val">${rp(loan.sisa_tagihan)}</div>
                        <div class="ref-sub">Sisa Tagihan</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="ref-val" style="color:#D97706;">${loan.sisa_tenor_label}</div>
                        <div class="ref-sub">Tenor</div>
                    </div>
                </div>
            `;
            list.appendChild(item);
        });
    }

    function toggleRefinance(cb) {
        const list = document.getElementById('refinance-list');
        const summary = document.getElementById('summary-box');
        if (cb.checked) {
            list.classList.add('show');
            summary.style.display = 'block';
        } else {
            list.classList.remove('show');
            summary.style.display = 'none';
            selectedLoans = [];
            renderLoanList();
            updateCalculations();
        }
    }

    function toggleLoan(id) {
        const idx = selectedLoans.indexOf(id);
        if (idx > -1) selectedLoans.splice(idx, 1);
        else selectedLoans.push(id);
        
        renderLoanList();
        updateCalculations();
    }

    function updateCalculations() {
        const nominal = parseFloat(document.querySelector('input[name="nominal"]').value) || 0;
        let totalSettlement = 0;
        
        const container = document.getElementById('pelunasan_inputs_container');
        container.innerHTML = '';

        activeLoans.forEach(loan => {
            if (selectedLoans.includes(loan.id)) {
                totalSettlement += parseFloat(loan.sisa_tagihan);
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'pelunasan_ids[]';
                input.value = loan.id;
                container.appendChild(input);
            }
        });

        document.getElementById('sum-nominal').textContent = rp(nominal);
        document.getElementById('sum-pelunasan').textContent = rp(totalSettlement);
        document.getElementById('sum-net').textContent = rp(nominal - totalSettlement);
        
        const netValue = document.getElementById('sum-net');
        if (nominal - totalSettlement < 0) netValue.classList.add('danger');
        else netValue.classList.remove('danger');
    }

    document.querySelector('input[name="nominal"]').addEventListener('input', function() {
        const max = parseFloat(this.getAttribute('max')) || 0;
        let val = parseFloat(this.value) || 0;
        
        if (val > max) {
            this.value = max;
            // Optional: alert or show toast that the amount is capped
        }
        updateCalculations();
    });
</script>
@endsection
