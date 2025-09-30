<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('negara')
                            ->label('Negara*')
                            ->required(),
                        // ->columnSpan(1),

                        // Forms\Components\Group::make()
                        // ->schema([
                        Forms\Components\TextInput::make('sea_fcl')
                            ->label('Sea FCL (days)')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('sea_lcl')
                            ->label('Sea LCL (days)')
                            ->numeric(),
                        // ])
                        // ->columnSpan(1),

                        // Forms\Components\Group::make()
                        // ->schema([
                        Forms\Components\TextInput::make('air_lcl')
                            ->label('Air LCL (days)')
                            ->numeric(),
                        Forms\Components\TextInput::make('air_fcl')
                            ->label('Air FCL (days)')
                            ->numeric(),
                        // ])
                        // ->columnSpan(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('negara')
                    ->label('Country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sea_fcl')
                    ->label('SEA FCL(days)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sea_lcl')
                    ->label('SEA LCL(days)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('air_lcl')
                    ->label('AIR LCL (days)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('air_fcl')
                    ->label('AIR FCL(days)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
