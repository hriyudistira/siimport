<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\DutyTax;
use Filament\Forms\Set;
use App\Models\Purchase;
use App\Models\Register;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Mask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DutyTaxResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DutyTaxResource\RelationManagers;


class DutyTaxResource extends Resource
{
    protected static ?string $model = DutyTax::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-currency-dollar';
    protected static ?string $navigationGroup = 'PO Import';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kode_po')
                    ->label('PO Number')
                    ->disabled(fn(?Model $record) => $record !== null) // Kalau mode edit (record ada), kolom disabled
                    ->options(
                        Register::whereNotIn('kode_po', DutyTax::pluck('kode_po'))->pluck('kode_po', 'kode_po')
                    )
                    ->searchable()
                    ->required()
                    ->reactive() // Penting agar bisa trigger event setelah pilih PO
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $register = Register::where('kode_po', $state)->first();
                        $set('supplier', $register?->supplier);
                    }),
                Forms\Components\TextInput::make('supplier')
                    ->label('Supplier Name')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bm')
                    ->label('Bea Masuk (BM)')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set, callable $get) {
                        $bm  = $get('bm') ?? 0;
                        $pph = $get('pph') ?? 0;
                        $ppn = $get('ppn') ?? 0;

                        $set('total', (int)$bm + (int)$pph + (int)$ppn);
                    }),
                Forms\Components\TextInput::make('pph')
                    ->label('Pajak Penghasilan (PPH)')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set, callable $get) {
                        $set('total', ($get('bm') ?? 0) + ($state ?? 0) + ($get('ppn') ?? 0));
                    }),
                Forms\Components\TextInput::make('ppn')
                    ->label('Pajak Pertambahan Nilai (PPN)')
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set, callable $get) {
                        $set('total', ($get('bm') ?? 0) + ($get('pph') ?? 0) + ($state ?? 0));
                    }),
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->required()
                    ->numeric()
                    ->disabled() // tidak bisa diedit
                    ->default(0)
                    ->dehydrated() // tetap disimpan ke database
                    ->afterStateHydrated(function ($component, $state) {
                        $component->state($state ?? 0);
                    })
                    ->extraAttributes(['class' => 'bg-gray-100 font-semibold text-right']),
                Forms\Components\TextInput::make('no_bill')
                    ->label('No. Billing')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('bill_date')
                    ->label('Billing Date')
                    ->required(),
                Forms\Components\fileUpload::make('doc_bill')
                    ->label('Document Billing')
                    ->directory('form-bill')
                    // ->visibility('private')
                    ->visibility('public') // Disimpan di storage:link
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->required(),
                Forms\Components\TextInput::make('no_ntpn')
                    ->label('NTPN Number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('ntpn_date')
                    ->label('NTPN Date')
                    ->required(),
                Forms\Components\fileUpload::make('doc_ntpn')
                    ->label('Document BPN')
                    ->directory('form-ntpn')
                    ->visibility('public') // Disimpan di storage:link
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->required(),
                Forms\Components\TextInput::make('cocc')
                    ->label('Biaya Handling')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('no_pay')
                    ->label('No. Payment')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('note')
                    ->label('Note')
                    // ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('bm')
                    ->label('Bea Masuk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pph')
                    ->label('PPH')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ppn')
                    ->label('PPN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_bill')
                    ->label('No. Billing')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bill_date')
                    ->label('Billing Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_bill')
                    ->label('Document BPN')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'No File';
                        $filePath = storage_path('app/public/' . $state);
                        if (!file_exists($filePath)) {
                            return 'File Missing';
                        }
                        // Batasi teks hanya 10 karakter, tapi tambahkan "..." jika terpotong
                        $displayName = basename($state);
                        return strlen($displayName) > 10
                            ? substr($displayName, 0, 10) . '...'
                            : $displayName;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_ntpn')
                    ->label('NTPN Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ntpn_date')
                    ->label('NTPN Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_ntpn')
                    ->label('Document NTPN')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'No File';
                        $filePath = storage_path('app/public/' . $state);
                        if (!file_exists($filePath)) {
                            return 'File Missing';
                        }
                        // Batasi teks hanya 10 karakter, tapi tambahkan "..." jika terpotong
                        $displayName = basename($state);
                        return strlen($displayName) > 10
                            ? substr($displayName, 0, 10) . '...'
                            : $displayName;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('cocc')
                    ->label('Biaya Handling')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_pay')
                    ->label('No. Payment')
                    ->searchable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
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
            'index' => Pages\ListDutyTaxes::route('/'),
            'create' => Pages\CreateDutyTax::route('/create'),
            'edit' => Pages\EditDutyTax::route('/{record}/edit'),
        ];
    }
    public static function getNavigationSort(): ?int
    {
        return 5; // semakin kecil, tampil di atas
    }
    public static function getNavigationGroup(): string
    {
        return 'PO Import';
    }
}
