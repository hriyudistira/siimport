<?php

namespace App\Filament\Resources\PurchaseResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Forwarders;
use Filament\Tables\Table;
use Forms\ComponentContainer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Resources\RelationManagers\HasManyRelationManager;
use Illuminate\Database\Eloquent\Relations\Relation;

class ForwardersRelationManager extends RelationManager

{

    protected static ?string $title = 'Forwarders';
    protected static ?string $relationshipName = 'forwarders';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('payment_type')->required(),
            Forms\Components\TextInput::make('incoterm')->required(),
            Forms\Components\DatePicker::make('etd_date')->required(),
            Forms\Components\DatePicker::make('eta_date')->required(),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('forwarder'),
                Tables\Columns\TextColumn::make('payment'),
                Tables\Columns\TextColumn::make('etd_date')->date(),
                Tables\Columns\TextColumn::make('eta_date')->date(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Inject supplier dari parent Purchase ke Forwarder
        $data['supplier'] = $this->getOwnerRecord()->supplier;
        $data['kode_po'] = $this->getOwnerRecord()->kode_po;

        return $data;
    }

    public static function getRelations(): array
    {
        return [
            ForwardersRelationManager::class,
        ];
    }
}
