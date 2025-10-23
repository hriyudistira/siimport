<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PurchaseTypeOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total PR', Purchase::query()->whereNotNull('kode_pr')->count())
                ->description('Total Purchase Request')
                ->color('info') // biru
                ->icon('heroicon-o-clipboard-document'),

            Stat::make('PO Produksi', Purchase::query()->where('kode_po', 'like', 'KPI%')->count())
                ->description('Purchase Order untuk Produksi')
                ->color('success') // hijau
                ->icon('heroicon-o-cog-6-tooth'),

            Stat::make('PO Non Produksi', Purchase::query()->where('kode_po', 'like', 'KNI%')->count())
                ->description('Purchase Order Non Produksi')
                ->color('warning') // kuning/oranye
                ->icon('heroicon-o-building-storefront'),
        ];
    }
}
