<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatusScheduleOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            // Baris pertama (1 kolom)
            Stat::make('Open PO', Schedule::query()->where('order_status', 'like', 'OOS%')->count())
                ->description('Open On Scheduled')
                ->color('primary')
                ->icon('heroicon-o-lock-open')
                ->extraAttributes(['class' => 'col-span-4']),
            Stat::make('Open PO', Schedule::query()->where('order_status', 'like', 'ODE%')->count())
                ->description('Open Delay')
                ->color('danger')
                ->icon('heroicon-o-inbox'),

            Stat::make('Close PO', Schedule::query()->where('order_status', 'like', 'ADE%')->count())
                ->description('Arrived Delay')
                ->color('info')
                ->icon('heroicon-o-face-frown'),

            // ini supaya saat di grid 4 kolom, dia span penuh 1 baris

            // Baris kedua (4 kolom sejajar)
            Stat::make('Close PO', Schedule::query()->where('order_status', 'like', 'AOS%')->count())
                // ->orWhere('order_status', 'like', 'NYS%')->orWhere('ship_status', 'like', 'DPO%')->count())
                ->description('Arrived On Schedule')
                ->color('success')
                ->icon('heroicon-o-face-smile'),
            // Stat::make('Close Shipment', Schedule::query()
            //     ->where('ship_status', 'like', 'APD%')
            //     ->orWhere('ship_status', 'like', 'APO%')
            //     ->count())
            //     ->description('Arrived Port Delay')
            //     ->color('warning')
            //     ->icon('heroicon-o-building-storefront'),



        ];
    }
}
