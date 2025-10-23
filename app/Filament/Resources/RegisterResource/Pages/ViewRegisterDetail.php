<?php

namespace App\Filament\Resources\RegisterResource\Pages;

use App\Filament\Resources\RegisterResource;
use Filament\Resources\Pages\Page;
use App\Models\Register;

class ViewRegisterDetail extends Page
{
    protected static string $resource = RegisterResource::class;

    protected static string $view = 'filament.resources.registers.detail';

    public $record;
    public function mount($record): void
    {
        $this->record = Register::with(['schedule', 'clearance', 'dutyTax', 'document'])
            ->findOrFail($record);
    }
}
