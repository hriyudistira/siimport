<?php

namespace App\Filament\Resources\ClearanceResource\Pages;

use App\Filament\Resources\ClearanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClearances extends ListRecords
{
    protected static string $resource = ClearanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
