<?php

namespace App\Filament\Resources\ClearanceResource\Pages;

use App\Filament\Resources\ClearanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClearance extends CreateRecord
{
    protected static string $resource = ClearanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
