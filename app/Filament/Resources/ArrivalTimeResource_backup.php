<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Purchase;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Table;
use App\Models\ArrivalTime;
use Illuminate\Support\Arr;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ArrivalTimeResource\Pages;
use App\Filament\Resources\ArrivalTimeResource\RelationManagers;

class ArrivalTimeResource extends Resource
{
    protected static ?string $model = ArrivalTime::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Onhold';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kode_po')
                    ->label('PO Number')
                    ->options(
                        Purchase::whereNotIn('kode_po', ArrivalTime::pluck('kode_po'))->whereNotIn('kode_po', ArrivalTime::pluck('kode_po'))->pluck('kode_po', 'kode_po')
                    )
                    ->searchable()
                    ->required()
                    ->reactive() // Penting agar bisa trigger event setelah pilih PO
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $purchase = Purchase::where('kode_po', $state)->first();
                        $set('supplier', $purchase?->supplier);
                    }),
                Forms\Components\TextInput::make('supplier')
                    ->label('Supplier Name')
                    // ->relationship('purchase', 'supplier')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('etd_date')
                    ->label('ETD Date'),
                // Forms\Components\DatePicker::make('etd_actdate'),
                Forms\Components\DatePicker::make('eta_date')
                    ->label('ETA Date'),
                // Forms\Components\DatePicker::make('eta_actdate'),
                Forms\Components\DatePicker::make('etacbi_date')
                    ->label('ETA CBI Date'),
                // Forms\Components\DatePicker::make('etacbi_actdate'),
                Forms\Components\DatePicker::make('rec_date')
                    ->label('Receipt Date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_po')
                    ->label('PO Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->label('Supplier Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('etd_date')
                    ->label('ETD Date')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('etd_actdate')
                //     ->label('ETD Actual Date')
                //     ->date()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('eta_date')
                    ->label('ETA Date')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('eta_actdate')
                //     ->label('ETA Actual Date')
                //     ->date()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('etacbi_date')
                    ->label('ETA CBI Date')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('etacbi_actdate')
                //     ->label('ETA CBI Actual Date')
                //     ->date()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('rec_date')
                    ->label('Receipt Date')
                    ->date()
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
            'index' => Pages\ListArrivalTimes::route('/'),
            'create' => Pages\CreateArrivalTime::route('/create'),
            'edit' => Pages\EditArrivalTime::route('/{record}/edit'),
        ];
    }
    public static function getNavigationSort(): ?int
    {
        return 2; // semakin kecil, tampil di atas
    }
    public static function getNavigationGroup(): string
    {
        return 'Onhold'; // Grup navigasi untuk ArrivalTimeResource
    }
}
