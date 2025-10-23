<?php

namespace App\Http\Controllers;

use App\Models\DutyTax;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DutyTaxReportController extends Controller
{
    public function print($year, $month)
    {
        $records = DutyTax::with('clearance') // ambil relasi ke tabel Clearance
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->orderBy('payment_date', 'asc')
            ->get();

        $total = $records->sum('total');
        $periode = Carbon::create($year, $month)->translatedFormat('F Y');

        $pdf = Pdf::loadView('filament.reports.duty-tax-summary', [
            'records' => $records, // <-- nama variabel disamakan dengan view
            'periode' => $periode,
            'total' => $total,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_PIB_{$periode}.pdf");
    }
}
