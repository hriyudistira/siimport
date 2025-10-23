<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class PurchasesChart extends ChartWidget
{
    protected static ?int $sort = 9;
    protected static ?string $heading = 'Pembelian Import Tahun 2025';

    protected function getData(): array
    {
        $data = Trend::model(Purchase::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->dateColumn('createpo') // gunakan kolom `createpo` sebagai dasar waktu
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Purchase Orders',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate)->toArray(),
                    'borderColor' => '#f59e0b', // optional: warna Amber sesuai theme
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)', // transparan
                    'fill' => true,
                ],
            ],
            // 'labels' => $data->map(fn(TrendValue $value) => $value->date)->toArray(),
            'labels' => $data->map(
                fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y') // 👉 format nama bulan + tahun
            )->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
