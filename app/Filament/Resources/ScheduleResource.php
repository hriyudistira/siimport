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
use Filament\Forms\Components\Actions\Action;
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
                 Forms\Components\DatePicker::make('delivery_date')
                    ->label('Requested Delivery')
                    ->required()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $set('ship_status', 'NYS'); // Update ke Shipment Status - Not Yet Schedule
                        } else {
                            $set('ship_status', null); // reset order status
                        }
                    }),
                Forms\Components\DatePicker::make('etacbi_date')
                    ->label('Plan ETA CBI Date')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $negara = $get('negara');
                        $shipBy = $get('ship_by');

                        if (!$negara || !$shipBy || !$state) {
                            $set('etacbi_date', null);
                            $set('doc_date', null);
                            $set('etd_date', null);
                            $set('etaport_date', null);
                            return;
                        }

                        // Ambil data country
                        $country = \App\Models\Country::where('negara', $negara)->first();

                        if (!$country) {
                            $set('etacbi_date', null);
                            $set('doc_date', null);
                            $set('etd_date', null);
                            $set('etaport_date', null);
                            return;
                        }
                        // Tentukan jumlah hari berdasarkan ship_by
                        $days = 0;

                        if (in_array($shipBy, ['sea_fcl', 'sea_lcl'])) {
                            $days = $country->sea_fcl;
                        } elseif ($shipBy === 'air_fcl') {
                            $days = $country->air_fcl;
                        }
                        // Hitung etaport_date = etacbi_date - 2 hari
                        $etaportDate = \Carbon\Carbon::parse($state)->subDays(2);
                        $set('etaport_date', $etaportDate->format('Y-m-d'));

                        // Hitung etd_date = etaport_date - days
                        $etdDate = $etaportDate->copy()->subDays($days);
                        $set('etd_date', $etdDate->format('Y-m-d'));

                        // Hitung doc_date = etd_date + 3 hari
                        $docDate = $etdDate->copy()->addDays(3);
                        $set('doc_date', $docDate->format('Y-m-d'));

                        // Logika order_status
                        $etaDate = Carbon::parse($state);
                        $deliveryDate = $get('delivery_date'); // pastikan field delivery_date ada di form

                        if ($deliveryDate) {
                            $deliveryDate = Carbon::parse($deliveryDate);
                            $set('ship_status', 'DOS'); // Update ke Shipment Status - Depart On Schedule
                            if ($etaDate->lessThanOrEqualTo($deliveryDate)) {
                                $set('order_status', 'OOS'); // Open On Schedule
                            } else {
                                $set('order_status', 'ODE'); // Open Delay
                            }
                        } else {
                            $set('order_status', null); // kalau delivery date kosong
                        }
                    }),
                
		Forms\Components\DatePicker::make('doc_date')
                    ->label('Document Date')
                    ->live(onBlur: true)
                    ->dehydrated(),
		Forms\Components\TextInput::make('order_status')
                    ->label('Order Status')
                    ->disabled() // otomatis, user tidak perlu input manual
                    ->dehydrated(true), // tetap disimpan di DB  
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
                    ->label('Actual ETD Date')
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $etdDate = $get('etd_date');

                        if ($state && $etdDate) {
                            $etdAct = Carbon::parse($state);
                            $etdDate = Carbon::parse($etdDate);

                            if ($etdAct->lessThanOrEqualTo($etdDate)) {
                                $set('ship_status', 'DOT'); // Depart On Time
                            } else {
                                $set('ship_status', 'DDE'); // Depart Delay
                            }
                        }
                    }),
                Forms\Components\DatePicker::make('etaport_actdate')
                    ->label('Actual ETAPort Date')
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $etaportDate = $get('etaport_date');

                        if ($state && $etaportDate) {
                            $etaportAct = Carbon::parse($state);
                            $etaportDate = Carbon::parse($etaportDate);

                            if ($etaportAct->lessThanOrEqualTo($etaportDate)) {
                                $set('ship_status', 'APO'); // Arrived Port On Time
                            } else {
                                $set('ship_status', 'APD'); // Arrived Port Delay
                            }
                        }
                    }),
                
                Forms\Components\DatePicker::make('etacbi_actdate')
                    ->label('Actual ETACBI Date')
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $deliveryDate = $get('delivery_date');

                        if ($state && $deliveryDate) {
                            $etacbiAct = Carbon::parse($state);
                            $deliveryDate = Carbon::parse($deliveryDate);

                            if ($etacbiAct->lessThanOrEqualTo($deliveryDate)) {
                                $set('order_status', 'AOS'); // Arrived On Schedule
                            } else {
                                $set('order_status', 'ADE'); // Arrived Delay
                            }
                        }
                    }),
                Forms\Components\TextInput::make('ship_status')
                    ->label('Shipment Status')
                    ->disabled() // otomatis, user tidak perlu input manual
                    ->dehydrated(true), // tetap disimpan di DB  
		//Forms\Components\Actions::make([
                  //  Action::make('update_status')
                    //    ->label('Update Status Order & Shipment')
                      //  ->color('primary')
                        //->action(function (Forms\Get $get, Forms\Set $set) {
                          //  $deliveryDate = $get('delivery_date') ? Carbon::parse($get('delivery_date')) : null;
                            //$etacbiPlan   = $get('etacbi_date') ? Carbon::parse($get('etacbi_date')) : null;
                            //$etacbiAct    = $get('etacbi_actdate') ? Carbon::parse($get('etacbi_actdate')) : null;

                            // // === Hitung order_status ===
                            //if ($etacbiAct) {
                              //  if ($deliveryDate && $etacbiAct->lte($deliveryDate)) {
                                //    $set('order_status', 'AOS'); // Arrived On Schedule
                               // } else {
                                 //   $set('order_status', 'ADE'); // Arrived Delay
                               // }
                            // } elseif ($etacbiPlan) {
                               // if ($deliveryDate && $etacbiPlan->lte($deliveryDate)) {
                                 //   $set('order_status', 'OOS'); // Open On Schedule
                                // } else {
                                   // $set('order_status', 'ODE'); // Open Delay
                                // }
                            // } else {
                               //  $set('order_status', null);
                           // }

                            // // === Hitung ship_status ===
                            //$etdDate     = $get('etd_date') ? Carbon::parse($get('etd_date')) : null;
                            //$etdActDate  = $get('etd_actdate') ? Carbon::parse($get('etd_actdate')) : null;
                            //$etaportDate = $get('etaport_date') ? Carbon::parse($get('etaport_date')) : null;
                            //$etaportAct  = $get('etaport_actdate') ? Carbon::parse($get('etaport_actdate')) : null;

                            //$status = null;

                            //if (!$etdActDate) {
                              //  $status = 'DOS'; // Depart Scheduled
                            //} elseif ($etdDate && $etdActDate->lte($etdDate)) {
                              //  $status = 'DOT'; // Depart On Time
                            //} elseif ($etdDate && $etdActDate->gt($etdDate)) {
                              //  $status = 'DDE'; // Depart Delay
                            //}

                            //if ($etaportAct) {
                              //  if ($etaportDate && $etaportAct->lte($etaportDate)) {
                                //    $status = 'APO'; // Arrived Port On Time
                                //} elseif ($etaportDate && $etaportAct->gt($etaportDate)) {
                                  //  $status = 'APD'; // Arrived Port Delay
                                //}
                            //}

                            //$set('ship_status', $status);
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('negara')
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Req Delivery')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etd_date') 
                    ->label('Plan ETD')
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
		Tables\Columns\TextColumn::make('order_status')
                    ->label('Order Status')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'OOS' => 'Open On Schedule',
                        'ODE' => 'Open Delay',
                        'AOS' => 'Arrived On Schedule',
                        'ADE' => 'Arrived Delay',
                        default => $state,
                    })
                    ->color(fn($state): string => match ($state) {
                        'OOS' => 'success',
                        'AOS' => 'success',
                        'ODE' => 'warning',
                        'ADE' => 'danger',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('ship_status')
                    ->label('Ship Status')
                    ->sortable()
                    ->searchable()
                    // ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'NYS' => 'Not Yet Scheduled',
                        'DOS' => 'Depart Scheduled',
                        'DOT' => 'Depart On Time',
                        'DDE' => 'Depart Delay',
                        'APO' => 'Arrived Port On Time',
                        'APD' => 'Arrived Port Delay',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'NYS' => 'gray',    // Not Yet Scheduled
                        'DOS' => 'warning',  // Depart Scheduled
                        'DOT' => 'success',  // Depart On Time
                        'DDE' => 'danger',   // Depart Delay
                        'APO' => 'success',  // Arrived Port On Time
                        'APD' => 'danger',   // Arrived Port Delay
                        default => 'gray',
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
