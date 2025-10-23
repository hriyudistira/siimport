<?php

namespace App\Filament\Resources\ArrivalTimeResource\Pages;

use App\Filament\Resources\ArrivalTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArrivalTime extends EditRecord
{
    protected static string $resource = ArrivalTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
