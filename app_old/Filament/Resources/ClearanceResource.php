<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Purchase;
use App\Models\Register;
use Filament\Forms\Form;
use App\Models\Clearance;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClearanceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClearanceResource\RelationManagers;

class ClearanceResource extends Resource
{
    protected static ?string $model = Clearance::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';
    protected static ?string $navigationGroup = 'PO Import';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kode_po')
                    ->label('PO Number')
                    ->disabled(fn(?Model $record) => $record !== null) // Kalau mode edit (record ada), kolom disabled
                    ->options(
                        Register::whereNotIn('kode_po', Clearance::pluck('kode_po'))->pluck('kode_po', 'kode_po')
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
                Forms\Components\TextInput::make('aju_pib')
                    ->label('No. Aju PIB')
                    ->required()
                    ->maxLength(255),
                Forms\Components\fileUpload::make('doc_pib')
                    ->label('Final Document PIB')
                    ->directory('form-pib')
                    // ->visibility('private')
                    ->visibility('public') // Disimpan di storage:link
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->required(),
                Forms\Components\TextInput::make('nopen_pib')
                    ->label('Nopen PIB')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('spb_date')
                    ->label('SPPB Date')
                    ->required(),
                Forms\Components\fileUpload::make('doc_spb')
                    ->label('Document SPPB')
                    ->directory('form-spb')
                    // ->visibility('private')
                    ->visibility('public') // Disimpan di storage:link
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->required(),
                Forms\Components\TextInput::make('cek_bc')
                    ->label('No. BC1.1')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('awb_master')
                    ->label('No. BL Master')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('awb_house')
                    ->label('No. BL House')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('awb_date')
                    ->label('MAWB/HAWB Date')
                    ->required(),
                Forms\Components\FileUpload::make('doc_awb')
                    ->label('Document MAWB/HAWB')
                    ->directory('form-awb')
                    // ->visibility('private')
                    ->visibility('public') // Disimpan di storage:link
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
		    ->multiple()//Untuk upload beberapa document
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->required(),
                Forms\Components\TextInput::make('no_invoice')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('invdoc_date')
                    ->label('Doc Invoice Date')
                    ->required(),
                Forms\Components\FileUpload::make('doc_invdoc')
                    ->label('Document Invoice/PL')
                    ->directory('form-invdoc')
                    // ->visibility('private')
                    ->visibility('public') // Disimpan di storage:link
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
                    ->multiple()//Upload bbrapa dokumen
                    ->downloadable()
                    ->openable()
                    ->preserveFilenames()
                    ->required(),
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
                Tables\Columns\TextColumn::make('aju_pib')
                    ->label('No. Aju PIB')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doc_pib')
                    ->label('Final Doc PIB')
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
                Tables\Columns\TextColumn::make('nopen_pib')
                    ->label('Nopen PIB')
                    ->searchable(),
                Tables\Columns\TextColumn::make('spb_date')
                    ->label('SPPB Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_spb')
                    ->label('Doc SPPB')
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
                Tables\Columns\TextColumn::make('cek_bc')
                    ->label('No. BC1.1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('awb_master')
                    ->label('No. BL Master')
                    ->searchable(),
                Tables\Columns\TextColumn::make('awb_house')
                    ->label('No. BL House')
                    ->searchable(),
                Tables\Columns\TextColumn::make('awb_date')
                    ->label('MAWB/HAWB Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_awb')
                    ->label('Doc MAWB/HAWB')
                    // Baca langsung dari record supaya tidak ketergantung pada state yang kosong
                    ->getStateUsing(function (Model $record) {
                        $raw = $record->doc_awb;
                        // Terima array atau string JSON
                        $files = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
                        if (!is_array($files) || empty($files)) {
                            return 'No File';
                        }
                        // Normalisasi backslash -> forward slash, lalu ambil nama file
                        $names = collect($files)
                            ->filter()
                            ->map(function ($path) {
                                $path = str_replace('\\', '/', $path);
                                $name = basename($path);
                                return strlen($name) > 15 ? substr($name, 0, 15) . '...' : $name;
                            })
                            ->join(', ');

                        return $names ?: 'No File';
                    }),
                Tables\Columns\TextColumn::make('no_invoice')
                    ->label('No. Invoice')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invdoc_date')
                    ->label('Invoice Doc Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_invdoc')
                    ->label('Doc Invoice')
                   ->getStateUsing(function (Model $record) {
                        $raw = $record->doc_invdoc;
                        $files = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
                        if (!is_array($files) || empty($files)) return 'No File';

                        return collect($files)->map(function ($p) {
                            $p = str_replace('\\', '/', $p);
                            $n = basename($p);
                            return strlen($n) > 15 ? substr($n, 0, 15) . '...' : $n;
                        })->join(', ');
                    }),
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
            'index' => Pages\ListClearances::route('/'),
            'create' => Pages\CreateClearance::route('/create'),
            'edit' => Pages\EditClearance::route('/{record}/edit'),
        ];
    }
    public static function getNavigationSort(): ?int
    {
        return 4; // semakin kecil, tampil di atas
    }
    public static function getNavigationGroup(): string
    {
        return 'PO Import';
    }
}
