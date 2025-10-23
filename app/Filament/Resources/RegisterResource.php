<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use App\Models\Country;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Purchase;
use App\Models\Register;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use function Laravel\Prompts\form;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RegisterResource\Pages;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RegisterResource\RelationManagers;

class RegisterResource extends Resource
{
    protected static ?string $model = Register::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'PO Import';
    protected static ?string $navigationLabel = 'PO Register';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kode_po')
                    ->label('PO Number')
                    ->disabled(fn(?Model $record) => $record !== null) // Kalau mode edit (record ada), kolom disabled
                    ->options(
                        Purchase::whereNotIn('kode_po', Register::pluck('kode_po'))->pluck('kode_po', 'kode_po')
                    )
                    ->searchable()
                    ->required()
                    ->reactive() // Penting agar bisa trigger event setelah pilih PO
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $purchase = Purchase::where('kode_po', $state)->first();
                        $set('supplier', $purchase?->supplier);
                        $gabungan = $purchase->item . ' | ' . $purchase->desc_item;
                        $set('buyer', $purchase?->buyer);
						$set('desc_item', $gabungan);
                    }),
                Forms\Components\TextInput::make('supplier')
                    ->label('Supplier Name')
                    // ->relationship('purchase', 'supplier')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated()
                    ->maxLength(255),
				Forms\Components\TextInput::make('desc_item')
                    ->label('Description Item')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated(true) // agar tetap disimpan
                    ->maxLength(255),
				Forms\Components\TextInput::make('buyer')
                    ->label('Buyer')
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated(true), // agar tetap disimpan
                Forms\Components\DatePicker::make('send_po_date')
                    ->label('Send PO Date')
                    ->required(),
                Forms\Components\Select::make('payment_type')
                    ->label('Payment Type')
                    ->required()
                    ->options([
                        '30days' => '30 Days',
                        'lc' => 'L/C',
                        'tt' => 'T/T',
                    ]),
                Forms\Components\Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'negara')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('negara')
                            ->required()
                            ->label('Nama Negara'),
                        TextInput::make('sea_fcl')
                            ->label('SEA FCL(days)')
                            ->required()
                            ->numeric(),
                        TextInput::make('sea_lcl')
                            ->label('SEA LCL(days)')
                            ->required()
                            ->numeric(),
                        TextInput::make('air_lcl')
                            ->label('AIR LCL(days)')
                            ->required()
                            ->numeric(),
                    ])
                    ->required()
                    ->live() // agar bisa trigger perubahan
                    ->afterStateUpdated(function ($state, Set $set) {
                        $country = \App\Models\Country::find($state);
                        if ($country) {
                            $set('country', $country->negara); // isi otomatis field country (varchar)
                        }
                    }),
                Forms\Components\Hidden::make('country')
                    ->dehydrated(true),
                Forms\Components\Select::make('ship_by')
                    ->label('Jalur Importasi')
                    ->required()
                    ->options([
                        'air_dhl' => 'Air-DHL',
                        'air_fedex' => 'Air-FedEx',
                        'air_ups' => 'Air-UPS',
                        'sea_fcl' => 'Sea-FCL',
                        'sea_lcl' => 'Sea-LCL',
                        // 'other' => 'Others',
                    ])
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        // Ambil 3 digit terakhir
                        if ($state) {
                            if (str_starts_with($state, 'sea')) {
                                // $set('container', substr($state, -3));
                                $set('container', substr($state, 0)); // ambil dari index ke-4
                            } else {
                                $set('container', 'air_lcl');
                            }
                        }
                    }),
                Forms\Components\Select::make('incoterms')
                    ->label('Incoterms')
                    ->required()
                    ->options([
                        'cfr' => 'CFR',
                        'cif' => 'CIF',
                        'dap' => 'DAP',
                        'ddp' => 'DDP',
                        'exw' => 'Ex Work',
                        'fob' => 'FOB',
                        'oth' => 'Others',
                    ]),
                Forms\Components\Select::make('ppjk')
                    ->label('PPJK')
                    ->options([
                        'aps' => 'APS',
                        'courier' => 'Courier',
                        'pmse' => 'PMSE',
                        'puninar' => 'Puninar',
                    ])
                    ->default(null),
				Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'registered' => 'Registered',
						 'scheduled' => 'Scheduled',
                        'documented' => 'Documented',
                        'clearanced' => 'Clearanced',
                        'closed' => 'Closed',
                    ]),
                Forms\Components\FileUpload::make('doc_evidence')
                    ->label('Proforma Invoice')
                    ->disk('public')
                    ->directory('form-evidence')
                    ->downloadable()
                    ->openable()
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(1024) // 1 MB
                    ->preserveFilenames()
                    ->visibility('public') // Disimpan di storage:link
                    ->required(),
				Forms\Components\FileUpload::make('doc_pr')
                    ->label('Document PR')
                    ->directory('form-pr') // storage/app/public/form-docs
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/pdf', 'image/*']) // Hanya PDF dan gambar
                    ->maxSize(10240) // Maksimal 10MB
                    ->multiple(true) // Bisa upload beberapa file
                    ->downloadable()
                    ->openable()
                    // ->required()
                    ->visibility('public'), // Disimpan di storage:link	
                Forms\Components\TextInput::make('container')
                    ->label('Container Code')
                    ->required()
                    ->disabled() // nonaktifkan jika tidak boleh diedit
                    ->dehydrated(true) // agar tetap disimpan
                    ->maxLength(20),
		//Forms\Components\Actions::make([
                  //  Action::make('update_item')
                    //    ->label('Update Item')
                      //  ->color('primary')
                        //->action(function (Get $get, Set $set) {
                          //  $kodePo = $get('kode_po');
                            //if (! $kodePo) return;

                            //$purchase = Purchase::where('kode_po', $kodePo)->first();
                            //if (! $purchase) return;

                            //$gabungan = trim(($purchase->item ?? '') . ' | ' . ($purchase->desc_item ?? ''), ' -');
                            //$set('desc_item', $gabungan);
                        //}),
                //])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_po')
                    ->label('PO Number')
                    ->sortable()
                    ->searchable()
					->color('primary')
					 ->url(fn($record) => RegisterResource::getUrl('view', ['record' => $record])),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable(),
				Tables\Columns\TextColumn::make('desc_item')
                    ->label('Item')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '';
                        // Pecah string berdasarkan tanda "|"
                        $parts = explode('|', $state);
                        return trim(end($parts)); // ambil bagian terakhir setelah "|"
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('send_po_date')
                    ->label('Send PO Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_evidence')
                    ->label('Proforma Invoice')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $file = basename($state);
                        return strlen($file) > 13 ? substr($file, 0, 10) . '...' : $file;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('incoterms')
                    ->formatStateUsing(fn($state) => strtoupper($state)) // tampilkan dalam huruf besar
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ship_by')
                    ->label('Jalur')
                    ->formatStateUsing(function (?string $state) {
                        if (!$state) return null;
                        return str_starts_with($state, 'air')
                            ? strtoupper(substr($state, 4))
                            : strtoupper(substr($state, 0, 3));
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('ppjk')
                    ->label('PPJK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('container')
                    ->formatStateUsing(fn($state) => strtoupper(substr($state, 4))) // tampilkan dalam huruf besar
                    ->searchable(),
					Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->color(fn($state) => match (strtolower($state)) {
                        'registered' => 'danger',
                        'documented' => 'primary',
                        'scheduled' => 'secondary',
                        'clearanced' => 'info',
                        'closed' => 'success',
                        default => 'secondary',
                    }),
                // Tables\Columns\TextColumn::make('created_at')
                    // ->dateTime()
                    // ->sortable()
                    // ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                    // ->dateTime()
                    // ->sortable()
                    // ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 🔹 Tambahkan filter status di sini
                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'registered'  => 'Registered',
                        'documented'  => 'Documented',
                        'scheduled'   => 'Scheduled',
                        'clearanced'  => 'Clearanced',
                        'closed'      => 'Closed',
                    ])
                    ->placeholder('All Status')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(function () {
                        // Tidak tampil kalau ada data yang terpilih sudah dipakai di documents
                        // Bisa juga langsung return false kalau mau disable permanen
                        return false;
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Register::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary'; // warna badge
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegisters::route('/'),
            'create' => Pages\CreateRegister::route('/create'),
            'edit' => Pages\EditRegister::route('/{record}/edit'),
			'view' => Pages\ViewRegisterDetail::route('/{record}'),
        ];
    }
}
