<?php

namespace App\Filament\Resources\RegisterResource\Pages;

use App\Filament\Resources\RegisterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRegister extends CreateRecord
{
    protected static string $resource = RegisterResource::class;

    // Return to Index after editing
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
	protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? 'registered';


        return $data;
    }
}
