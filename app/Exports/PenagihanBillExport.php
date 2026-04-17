<?php

namespace App\Exports;

use App\Models\PenagihanBill;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PenagihanBillExport implements FromView, ShouldAutoSize
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $tagihan = PenagihanBill::with(['details' => function($q) {
            $q->whereNull('deleted_at')->with('anggota');
        }])->findOrFail($this->id);

        return view('penagihan.export_excel', compact('tagihan'));
    }
}
