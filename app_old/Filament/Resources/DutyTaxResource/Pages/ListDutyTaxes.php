<?php

namespace App\Filament\Resources\DutyTaxResource\Pages;

use App\Filament\Resources\DutyTaxResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDutyTaxes extends ListRecords
{
    protected static string $resource = DutyTaxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
