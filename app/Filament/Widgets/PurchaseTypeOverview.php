<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PurchaseTypeOverview extends BaseWidget
{
    protected static ?int $sort = 7;

    // atur grid per breakpoint
    // 1 kolom untuk semua screen kecil, 
    // tapi 2 row: baris pertama 1 stat, baris kedua 4 stat
    protected int | string | array $columns = [
        'default' => 1,  // hp / layar kecil
        'md' => 2,       // tablet
        'lg' => 4,       // desktop
    ];
    
    protected function getStats(): array
    {
        return [
            // Baris pertama (1 kolom)
            Stat::make('Total PR', Purchase::query()->whereNotNull('kode_pr')->count())
                ->description('Total Purchase Request')
                ->color('info')
                ->icon('heroicon-o-clipboard-document')
                ->extraAttributes(['class' => 'col-span-4']),
            // ini supaya saat di grid 4 kolom, dia span penuh 1 baris

            // Baris kedua (4 kolom sejajar)
            Stat::make('PO Produksi', Purchase::query()->where('kode_po', 'like', 'KPI%')->count())
                ->description('Purchase Order untuk Produksi')
                ->color('success')
                ->icon('heroicon-o-cog-6-tooth'),
            Stat::make('PO Non Produksi', Purchase::query()->where('kode_po', 'like', 'KNI%')->count())
                ->description('Purchase Order Non Produksi')
                ->color('warning')
                ->icon('heroicon-o-building-storefront'),

            Stat::make('PO Sparepart', Purchase::query()->where('kode_po', 'like', 'KSI%')->count())
                ->description('Purchase Order P/N Sparepart')
                ->color('danger')
                ->icon('heroicon-o-wrench'),

            Stat::make('PO Asset', Purchase::query()->where('kode_po', 'like', 'KAI%')->count())
                ->description('Purchase Order Asset')
                ->color('primary')
                ->icon('heroicon-o-home-modern'),
        ];
    }
}
