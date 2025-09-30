<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
// use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         \Filament\Actions\EditAction::make(),
    //     ];
    // }
    // protected function getFooterWidgets(): array
    // {
    //     return [
    //         DocumentResource\Widgets\DocumentViewer::class,
    //     ];
    // }
}
