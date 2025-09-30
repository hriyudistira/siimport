<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\DocumentResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder;


class ManageDocuments extends ManageRecords
{
    protected static string $resource = DocumentResource::class;

    public function mount(): void
    {
        parent::mount();

        // Simpan tab ke session untuk digunakan oleh form
        if (request()->has('activeTab')) {
            session(['activeTab' => request()->input('activeTab')]);
        }
    }

    public function getTabs(): array
    {
        return [
            'plan' => Tab::make('Plan')
                ->label('Plan')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('inv_date'))
                ->url(DocumentResource::getUrl(name: 'index', parameters: ['activeTab' => 'plan'])),

            'actual' => Tab::make('Actual')
                ->label('Actual')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('inv_actdate'))
                ->url(DocumentResource::getUrl(name: 'index', parameters: ['activeTab' => 'actual'])),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Data'),
        ];
    }
}
