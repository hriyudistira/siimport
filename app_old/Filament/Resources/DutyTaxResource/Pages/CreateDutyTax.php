<?php

namespace App\Filament\Resources\DutyTaxResource\Pages;

use App\Filament\Resources\DutyTaxResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDutyTax extends CreateRecord
{
    protected static string $resource = DutyTaxResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
