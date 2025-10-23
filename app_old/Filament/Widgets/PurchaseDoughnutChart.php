<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;

use Filament\Widgets\ChartWidget;

class PurchaseDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Supplier Distribution';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Purchase::selectRaw('kode_supplier, COUNT(*) as total')
            ->where('kode_supplier', 'like', '%SPI%') // Filter for Purchase Orders Produksi
            ->groupBy('kode_supplier')
            ->pluck('total', 'kode_supplier')
            ->toArray();
        $labels = array_keys($data);
        $values = array_values($data);
        $colors = collect($labels)->map(function ($label) {
            return sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Generate random color for each label
        })->toArray();
        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'hoverOffset' => 4,
                ],
            ],
            // 'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
