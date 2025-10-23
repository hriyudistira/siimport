<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    // Return ke Index saat selesai create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
