<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Purchase;
use Filament\Forms\Form;
use Filament\Forms\Set;
use App\Models\Forwarder;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ForwarderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ForwarderResource\RelationManagers;

class ForwarderResource extends Resource
{
    protected static ?string $model = Forwarder::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Schedule';
    protected static ?string $navigationLabel = 'Forwarders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('kode_po')
                    ->label('PO Number')
                    ->options(
                        Purchase::whereNotIn('kode_po', Forwarder::pluck('kode_po'))->pluck('kode_po', 'kode_po')
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
                Forms\Components\Select::make('payment_type')
                    ->label('Payment Type')
                    ->required()
                    ->options([
                        '30days' => '30 Days',
                        'lc' => 'L/C',
                        'tt' => 'T/T',
                    ]),
                Forms\Components\DatePicker::make('send_po_date')
                    ->label('Send PO Date')
                    ->required(),
                Forms\Components\FileUpload::make('doc_evidence')
                    ->label('Document Evidence')
                    ->directory('form-evidence')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
                    ->preserveFilenames()
                    ->required(),
                Forms\Components\Select::make('incoterms')
                    ->label('Incoterms')
                    ->required()
                    ->options([
                        'cfr' => 'CFR',
                        'cif' => 'CIF',
                        'exw' => 'Ex Works',
                        'fob' => 'FOB',
                        'oth' => 'Others',
                    ]),

                Forms\Components\Select::make('ship_by')
                    ->label('Ship By')
                    ->required()
                    ->options([
                        'air' => 'Air',
                        'dhl' => 'DHL',
                        'fedex' => 'FedEx',
                        'sea' => 'Sea',
                        'tnt' => 'TNT',
                        'others' => 'Others',
                    ]),

                Forms\Components\Select::make('ppjk')
                    ->label('PPJK')
                    ->required()
                    ->options([
                        'aps' => 'APS',
                        'courier' => 'Courier',
                        'pmse' => 'PMSE',
                        'puninar' => 'Puninar',
                    ]),
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
                Tables\Columns\TextColumn::make('payment_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('send_po_date')
                    ->label('Send PO Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_evidence')
                    ->label('Document Evidence')
                    ->searchable(),
                Tables\Columns\TextColumn::make('incoterms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ship_by')
                    ->label('Ship By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ppjk')
                    ->label('PPJK')
                    ->searchable(),
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
            'index' => Pages\ListForwarders::route('/'),
            'create' => Pages\CreateForwarder::route('/create'),
            'edit' => Pages\EditForwarder::route('/{record}/edit'),
        ];
    }
    public static function getNavigationSort(): ?int
    {
        return 1; // semakin kecil, tampil di atas
    }
    public static function getNavigationGroup(): string
    {
        return 'Schedule';
    }
}
