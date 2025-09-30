<?php

namespace App\Filament\Resources\DutyTaxResource\Pages;

use App\Filament\Resources\DutyTaxResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDutyTax extends EditRecord
{
    protected static string $resource = DutyTaxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    // Return to Index after editing
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
