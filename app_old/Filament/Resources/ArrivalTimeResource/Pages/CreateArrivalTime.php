<?php

namespace App\Filament\Resources\ArrivalTimeResource\Pages;

use App\Filament\Resources\ArrivalTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArrivalTime extends CreateRecord
{
    protected static string $resource = ArrivalTimeResource::class;

	protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }	
}
