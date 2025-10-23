<?php

namespace App\Filament\Resources\ForwarderResource\Pages;

use App\Filament\Resources\ForwarderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateForwarder extends CreateRecord
{
    protected static string $resource = ForwarderResource::class;

protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
