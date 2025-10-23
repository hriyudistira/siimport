<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Purchase;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PurchaseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PurchaseResource\RelationManagers;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';
    protected static ?string $navigationGroup = 'Purchase';
    protected static ?string $navigationLabel = 'Purchase Import';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_po')->label('PO Number')
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'sticky right-0 bg-red-50 z-10', // adjust left offset
                    ])
                    ->disabled(fn(?Model $record) => $record !== null), // Kalau mode edit (record ada), kolom disabled,
                Tables\Columns\TextColumn::make('line_po')->label('Line'),
                Tables\Columns\TextColumn::make('createpo')->label('Create PO')->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('kode_supplier')->label('Kode Supplier')->searchable(),
                Tables\Columns\TextColumn::make('supplier')->label('Supplier Name')->searchable(),
                // ->extraAttributes([
                //     'class' => 'sticky left-[150px] bg-white z-10', // adjust left offset
                // ]),
                Tables\Columns\TextColumn::make('item'),
                Tables\Columns\TextColumn::make('desc_item')->label('Deskripsi')->searchable(),
                Tables\Columns\TextColumn::make('qty')->label('Quantity'),
                Tables\Columns\TextColumn::make('harga'),
		Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('kode_pr')->label('Kode PR')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePurchases::route('/'),
        ];
    }
}
