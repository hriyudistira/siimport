<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Country;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Purchase;
use App\Models\Register;
use App\Models\Schedule;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ScheduleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ScheduleResource\RelationManagers;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'PO Import';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kode_po')
                    ->label('PO Number')
                    ->disabled(fn(?Model $record) => $record !== null) // Kalau mode edit (record ada), kolom disabled
                    ->options(
                        Register::whereNotIn('kode_po', Schedule::pluck('kode_po'))->pluck('kode_po', 'kode_po')
                    )
                    ->searchable()
                    ->required()
                    ->reactive() // Penting agar bisa trigger event setelah pilih PO
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $register = Register::where('kode_po', $state)->first();
                        $set('supplier', $register?->supplier);
                        $set('negara', $register?->country);
                        $set('ship_by', $register?->container);
                    }),
                Forms\Components\TextInput::make('supplier')
                    ->label('Supplier Name')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated()
                    ->maxLength(255),
                Forms\Components\TextInput::make('negara')
                    ->label('Country')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ship_by')
                    ->label('Ship By')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated(),
                Forms\Components\DatePicker::make('etacbi_date')
                    ->label('Plan ETA CBI Date')
                    // ->reactive()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        // Ambil parameter
                        if ($state) {
                            $set('etaport_date', Carbon::parse($state)->subDays(3)->toDateString());
                        } else {
                            $set('etaport_date', null);
                        }
                        $negara = $get('negara');
                        $shipBy = $get('ship_by');

                        if (!$negara || !$shipBy || !$state) {
                            $set('etacbi_date', null);
                            $set('delivery_date', null);
                            $set('etd_date', null);
                            return;
                        }

                        // Ambil data country
                        $country = \App\Models\Country::where('negara', $negara)->first();

                        if (!$country) {
                            $set('etacbi_date', null);
                            $set('delivery_date', null);
                            $set('etd_date', null);
                            return;
                        }
                        // Tentukan jumlah hari berdasarkan ship_by
                        $days = 0;

                        if (in_array($shipBy, ['sea_fcl', 'sea_lcl'])) {
                            $days = $country->sea_fcl;
                        } elseif ($shipBy === 'air_fcl') {
                            $days = $country->air_fcl;
                        } // Tambah kondisi lain jika perlu
                        // Tambahkan hari ke delivery_date
                        $rumus = \Carbon\Carbon::parse($state)->subDays($days - 1);
                        // Set ke etd_date
                        $set('etd_date', $rumus->format('Y-m-d'));

                        // Logika untuk etaport_date
                        if ($shipBy === 'sea_fcl') {

                            $eta = $rumus->copy()->subDays(4);
                            $set('delivery_date', $eta->format('Y-m-d'));
                        } elseif ($shipBy === 'sea_lcl' or $shipBy === 'air_lcl') {
                            $eta = $rumus->copy()->subDays(4);
                            $set('delivery_date', $eta->format('Y-m-d'));
                        } else {
                            $set('delivery_date', null); // untuk air, misalnya
                        }
                    }),

                Forms\Components\DatePicker::make('etaport_date')
                    ->label('Plan ETAPort Date')
                    ->live(onBlur: true)
                    ->dehydrated(),
                Forms\Components\DatePicker::make('delivery_date')
                    ->label('Document Date')
                    ->live(onBlur: true)
                    ->dehydrated(),
                Forms\Components\DatePicker::make('etd_date')
                    ->label('Plan ETD Date')
                    ->live(onBlur: true)
                    ->dehydrated(),
                Forms\Components\DatePicker::make('etd_actdate')
                    ->label('Actual ETD Date'),
                Forms\Components\DatePicker::make('etaport_actdate')
                    ->label('Actual ETAPort Date'),
                Forms\Components\DatePicker::make('etacbi_actdate')
                    ->label('Actual ETACBI Date'),
                Forms\Components\DatePicker::make('rec_date')
                    ->label('Receipt Date'),
            ]);
    }
    // protected static function hitungEtd(Forms\Set $set, Forms\Get $get): void
    // {
    //     $negara = $get('negara');
    //     $shipBy = $get('ship_by');
    //     $deliveryDate = $get('delivery_date');

    //     if (! $negara || ! $shipBy || ! $deliveryDate) {
    //         $set('etd_date', null);
    //         return;

    //         // Ambil nilai hari dari field sesuai ship_by
    //         $leadTime = \App\Models\Country::where('negara', $negara)
    //             ->value($shipBy); // langsung ambil field: sea_fcl, sea_lcl, etc

    //         if ($leadTime !== null) {
    //             $eta = \Carbon\Carbon::parse($deliveryDate)->addDays($leadTime);
    //             $set('etd_date', $eta->toDateString());
    //         } else {
    //             $set('etd_date', null);
    //         }
    //     }
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_po')
                    ->label('PO Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('negara')
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Delivery Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etd_date') // field pengganti eta_date
                    ->label('Plan ETA')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etaport_date')
                    ->label('Plan ETA Port')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etacbi_date')
                    ->label('Plan ETA CBI')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etd_actdate')
                    ->label('Actual ETD')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etaport_actdate')
                    ->label('Actual ETA Port')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etacbi_actdate')
                    ->label('Actual ETA CBI')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('rec_date')
                //     ->date()
                //     ->sortable(),
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

    public static function getNavigationBadge(): ?string
    {
        return (string) Schedule::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary'; // warna badge
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
