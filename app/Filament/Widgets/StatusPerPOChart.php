<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Widgets\ChartWidget;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;

class StatusPerPOChart extends ChartWidget
{
    protected static ?string $heading = 'Order Status PO Import';
    protected static ?int $sort = 2;
    protected function getData(): array
    {
        $statusList = [
            'ODE' => 'Open Delay',
            'OOS' => 'Open Scheduled',
            'ADE' => 'Arrival Delay',
            'AOS' => 'Arrival On Schedule',
        ];

        // Query grouping berdasarkan bulan dan status
        $rows = Schedule::selectRaw("DATE_FORMAT(etd_date, '%b %Y') as bulan, order_status, COUNT(*) as jumlah")
            ->groupBy('bulan', 'order_status')
            ->orderBy(DB::raw("MIN(etd_date)"))
            ->get();

        $labels = $rows->pluck('bulan')->unique()->values()->all();

        $datasets = [];
        $colors = [
            'ODE' => '#FF6384', // merah
            'OOS' => '#FFCE56', // kuning
            'ADE' => '#36A2EB', // biru
            'AOS' => '#4BC0C0', // hijau
        ];

        foreach ($statusList as $status => $label) {
            $data = [];
            foreach ($labels as $bulan) {
                $data[] = $rows->where('bulan', $bulan)->where('order_status', $status)->sum('jumlah');
            }

            $datasets[] = [
                'label' => $label,
                'data' => $data,
                'backgroundColor' => $colors[$status],
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'datalabels' => [
                    'color' => '#000',
                    'anchor' => 'end',
                    'align' => 'top',
                    'formatter' => RawJs::make(<<<'JS'
                        function(value, context) {
                            let total = 0;
                            context.chart.data.datasets.forEach(ds => {
                                if (ds.data[context.dataIndex] !== undefined) {
                                    total += ds.data[context.dataIndex];
                                }
                            });

                            if (total === 0) return '';
                            let percentage = ((value / total) * 100).toFixed(1);

                            return value + ' (' + percentage + '%)';
                        }
                    JS),
                    'font' => [
                        'weight' => 'bold',
                    ],
                ],
            ],
            'scales' => [
                'x' => ['stacked' => true],
                'y' => [
                    'stacked' => true,
                    'title' => ['display' => true, 'text' => 'Jumlah PO'],
                ],
            ],
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '300px'; // samakan nilainya di semua chart
    }
}
