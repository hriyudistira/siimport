<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Document;
use App\Models\Register;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\DocumentResource\Pages;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'PO Import';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kode_po')
                    ->label('PO Number')
                    ->disabled(fn(?Model $record) => $record !== null) // Kalau mode edit (record ada), kolom disabled
                    ->options(
                        Register::whereNotIn('kode_po', Document::pluck('kode_po'))->pluck('kode_po', 'kode_po')
                    )
                    ->searchable()
                    ->required()
                    ->reactive() // Penting agar bisa trigger event setelah pilih PO
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $register = Register::where('kode_po', $state)->first();
                        $set('supplier', $register?->supplier);
                    }),
                Forms\Components\TextInput::make('supplier')
                    ->required()
                    ->disabled() // Nonaktifkan input ini karena akan diisi otomatis
                    ->dehydrated()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('biel_actdate')
                    ->label('Actual B/L Date')
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (!$state) {
                            $set('piel_date', null);
                            $set('biel_date', null);
                            $set('cod_date', null);
                            $set('ins_date', null);
                            $set('inv_date', null);
                            return;
                        }

                        $deliveryDate = Carbon::parse($state);

                        // Otomatis set piel_date = delivery_date - 8 hari
                        $invDate = $deliveryDate->copy()->subDays(8);
                        $set('inv_date', $invDate->format('Y-m-d'));

                        // Otomatis set piel_date = delivery_date - 5 hari
                        $pielDate = $deliveryDate->copy()->subDays(5);
                        $set('piel_date', $pielDate->format('Y-m-d'));

                        // Otomatis set biel_date = delivery_date - 3 hari
                        $bielDate = $deliveryDate->copy()->subDays(3);
                        $cooDate = $deliveryDate->copy()->subDays(3);
                        $insDate = $deliveryDate->copy()->subDays(3);
                        $set('biel_date', $bielDate->format('Y-m-d'));
                        $set('cod_date', $cooDate->format('Y-m-d'));
                        $set('ins_date', $insDate->format('Y-m-d'));
                    }),
                Forms\Components\DatePicker::make('inv_date')->label('Plan Invoice Date'),
                Forms\Components\DatePicker::make('biel_date')->label('Plan B/L Date'),
                Forms\Components\DatePicker::make('piel_date')->label('Plan P/L Date'),
                Forms\Components\DatePicker::make('cod_date')->label('Plan COO Date'),
                Forms\Components\FileUpload::make('doc_permit')
                    ->label('Document FTA/Izin/COA/COO')
                    ->directory('form-permits') // storage/app/public/form-docs
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/pdf', 'image/*']) // Hanya PDF dan gambar
                    ->maxSize(10240) // Maksimal 10MB
                    ->multiple(true) // Bisa upload beberapa file
                    ->downloadable()
                    ->openable()
                    //->required()
                    ->visibility('public'), // Disimpan di storage:link
                Forms\Components\DatePicker::make('inv_actdate')->label('Actual Invoice Date'),
                Forms\Components\DatePicker::make('piel_actdate')->label('Actual P/L Date'),
                Forms\Components\DatePicker::make('cod_actdate')->label('Actual COO Date'),
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
                Tables\Columns\TextColumn::make('inv_date')
                    ->label('Invoice Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('biel_date')
                    ->label('B/L Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ins_date')
                    ->label('Insurance Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('piel_date')
                    ->label('P/L Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cod_date')
                    ->label('COO Date')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('inv_actdate')
                //     ->date()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('biel_actdate')
                    ->label('Actual B/L Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ins_actdate')
                    ->label('Actual Insurance Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('piel_actdate')
                    ->label('Actual P/L Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cod_actdate')
                    ->label('Actual COO Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_permit')
                    ->label('Doc FTA/Izin/COA/COO')
                    ->getStateUsing(function (Model $record) {
                        $raw = $record->doc_permit;
                        $files = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
                        if (!is_array($files) || empty($files)) return 'No File';

                        return collect($files)->map(function ($p) {
                            $p = str_replace('\\', '/', $p);
                            $n = basename($p);
                            return strlen($n) > 15 ? substr($n, 0, 15) . '...' : $n;
                        })->join(', ');
                    }),
                // ->url(function ($record) {
                //     // Ambil IP address server secara dinamis
                //     $serverIp = request()->getSchemeAndHttpHost();
                //     // Contoh hasil: http://192.168.1.100 atau http://domain.com

                //     return $record->doc_permit
                //         ? $serverIp . '/storage/' . $record->doc_permit
                //         : null;
                // })
                // ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('doc_pl')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('doc_insurance')
                //     ->searchable(),
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
            ->actions([ // Tambahkan ini untuk fungsi View Document
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
            'view' => Pages\ViewDocument::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Document::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary'; // warna badge
    }
}
