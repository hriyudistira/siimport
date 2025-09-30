<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

// Return ke Index saat selesai create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
