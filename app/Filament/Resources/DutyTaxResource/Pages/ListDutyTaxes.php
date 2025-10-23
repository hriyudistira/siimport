<?php

namespace App\Filament\Resources\DutyTaxResource\Pages;

use App\Filament\Resources\DutyTaxResource;
use Filament\Actions;
use App\Models\DutyTax;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListDutyTaxes extends ListRecords
{
    protected static string $resource = DutyTaxResource::class;

    protected function getHeaderActions(): array
    {
         return [
            Actions\CreateAction::make(),

            Actions\Action::make('cetakSummary')
                ->label('📄 List PIB Berkala')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->form([
                    Select::make('year')
                        ->label('Tahun')
                        ->options(
                            DutyTax::selectRaw('YEAR(payment_date) as year')
                                ->whereNotNull('payment_date')
                                ->groupBy('year')
                                ->orderBy('year', 'desc')
                                ->pluck('year', 'year')
                        )
                        ->default(now()->year)
                        ->required(),

                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required(),
                ])
                ->action(function (array $data, $livewire) {
                    // Buat URL ke route laporan dengan parameter tahun & bulan
                    $url = route('duty-tax.report', [
                        'year' => $data['year'],
                        'month' => $data['month'],
                    ]);

                    // Tampilkan notifikasi
                    Notification::make()
                        ->title('Laporan dibuka di tab baru...')
                        ->success()
                        ->send();

                    // Buka tab baru langsung dari browser
                    $livewire->js("window.open('{$url}', '_blank')");
                }),
        ];
    }
}
