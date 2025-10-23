<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Widgets\ChartWidget;

class OrderDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Order Status PO Import';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Mapping status ke kategori
        $statusMap = [
            'ODE' => 'Open',
            'OOS' => 'Open',
            'ADE' => 'Close',
            'AOS' => 'Close',
        ];

        // Ambil data order_status dari DB
        $rawData = Schedule::selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        // Inisialisasi hasil akhir
        $groupedData = [
            'Open' => 0,
            'Close' => 0,
        ];

        foreach ($rawData as $status => $count) {
            $category = $statusMap[$status] ?? null;
            if ($category) {
                $groupedData[$category] += $count;
            }
        }

        $total = array_sum(array_values($groupedData));

        // Format label dengan nilai dan persentase
        $formattedLabels = [];
        foreach ($groupedData as $category => $count) {
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $formattedLabels[] = "$category: $count ($percentage%)";
        }

        return [
            'datasets' => [
                [
                    'data' => array_values($groupedData),
                    'backgroundColor' => ['#FF9800', '#4CAF50'],
                    'hoverOffset' => 4,
                    // Menambahkan label pada setiap segmen
                    // 'datalabels' => [
                    //     'color' => '#FFFFFF',
                    //     'font' => [
                    //         'weight' => 'bold',
                    //         'size' => 14,
                    //     ],
                    // ],

                ],
            ],
            'labels' => $formattedLabels, // Label sudah termasuk nilai
        ];
    }

    protected function getOptions(): array
    // {
    //     // $total = array_sum($this->getData()['datasets'][0]['data']);

    //     return [
    //         'responsive' => true,
    //         'maintainAspectRatio' => true,
    //         'plugins' => [
    //             'legend' => [
    //                 'display' => true,
    //                 'position' => 'bottom',
    //             ],
    //             'tooltip' => [
    //                 'enabled' => true,
    //             ],
    //         ],
    //         // Menambahkan teks di tengah chart
    //         'animation' => [
    //             'animateScale' => true,
    //             'animateRotate' => true,
    //         ],
    //     ];
    // }
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'scales' => [
                'y' => [
                    'grid' => [
                        'display' => false, // Menghilangkan grid horizontal
                    ],
                    'ticks' => [
                        'display' => false, // Memastikan ticks tidak ditampilkan
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false, // Menghilangkan grid vertikal
                    ],
                    'ticks' => [
                        'display' => false, // Memastikan ticks tidak ditampilkan
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    // TAMBAHKAN KONFIGURASI INI UNTUK MEMBUAT LEGEND LEBIH BESAR
                    'labels' => [
                        'font' => [
                            'size' => 16,    // Ukuran font lebih besar
                            'weight' => 'bold', // Tebal
                            'family' => "'Inter', 'Segoe UI', sans-serif", // Jenis font
                        ],
                        'padding' => 20,     // Jarak antar legend
                        'usePointStyle' => true, // Gunakan point style
                        'pointStyle' => 'circle',
                        'boxWidth' => 16,    // Lebar box legend
                        'boxHeight' => 16,   // Tinggi box legend
                    ]
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(30, 41, 59, 0.9)',
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                    'bodyFont' => [
                        'size' => 14,
                    ],
                    'padding' => 12,
                ],
                // Untuk datalabels (jika menggunakan plugin)
                'datalabels' => [
                    'color' => '#fff',
                    'font' => [
                        'weight' => 'bold',
                        'size' => 14,
                    ],
                    'formatter' => function ($value, $context) {
                        $total = array_sum($context->dataset->data);
                        $percentage = $total > 0 ? round(($value / $total) * 100, 1) : 0;
                        return $percentage . '%';
                    }
                ]
            ],
            // Menambahkan teks di tengah chart
            'animation' => [
                'animateScale' => true,
                'animateRotate' => true,
            ],
            // Tambahkan padding untuk mengakomodasi legend yang lebih besar
            'layout' => [
                'padding' => [
                    'bottom' => 40, // Tambah padding bawah untuk legend besar
                ]
            ]
        ];
    }
    protected function getMaxHeight(): ?string
    {
        return '300px'; // samakan nilainya di semua chart
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
