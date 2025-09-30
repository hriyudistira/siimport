<?php

namespace App\Filament\Resources\ArrivalTimeResource\Pages;

use App\Filament\Resources\ArrivalTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArrivalTimes extends ListRecords
{
    protected static string $resource = ArrivalTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
