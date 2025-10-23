<?php

namespace App\Filament\Resources;

use App\Models\Document;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DocumentResource\Pages\ManageDocuments;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Schedule';

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('kode_po')->label('Kode PO')->searchable()->sortable(),
    //             Tables\Columns\TextColumn::make('supplier')->label('Supplier')->searchable()->sortable(),

    //             // Plan columns
    //             Tables\Columns\TextColumn::make('inv_date')->label('Inv Date')->date(),
    //             Tables\Columns\TextColumn::make('biel_date')->label('Biel Date')->date(),
    //             Tables\Columns\TextColumn::make('piel_date')->label('Piel Date')->date(),
    //             Tables\Columns\TextColumn::make('cod_date')->label('Cod Date')->date(),

    //             // Actual columns
    //             Tables\Columns\TextColumn::make('inv_actdate')->label('Inv Actdate')->date(),
    //             Tables\Columns\TextColumn::make('biel_actdate')->label('Biel Actdate')->date(),
    //             Tables\Columns\TextColumn::make('piel_actdate')->label('Piel Actdate')->date(),
    //             Tables\Columns\TextColumn::make('cod_actdate')->label('Cod Actdate')->date(),
    //         ]);
    // }
    public static function table(Table $table): Table
    {
        $activeTab = request()->input('activeTab', 'plan'); // default tab

        $commonColumns = [
            Tables\Columns\TextColumn::make('kode_po')->label('Kode PO')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('supplier')->label('Supplier')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('doc_permit')->label('Document Permit'),
            Tables\Columns\TextColumn::make('doc_pl')->label('Document P/L'),
        ];

        $planColumns = [
            Tables\Columns\TextColumn::make('inv_date')->label('Plan Invoice Date')->date(),
            Tables\Columns\TextColumn::make('biel_date')->label('Plan B/L Date')->date(),
            Tables\Columns\TextColumn::make('piel_date')->label('Plan P/L Date')->date(),
            Tables\Columns\TextColumn::make('cod_date')->label('Plan COO Date')->date(),
        ];

        $actualColumns = [];

        if ($activeTab !== 'plan') {
            $actualColumns = [
                Tables\Columns\TextColumn::make('inv_actdate')->label('Act Invoice Date')->date(),
                Tables\Columns\TextColumn::make('biel_actdate')->label('Act B/L Date')->date(),
                Tables\Columns\TextColumn::make('piel_actdate')->label('Act P/L Date')->date(),
                Tables\Columns\TextColumn::make('cod_actdate')->label('Act COO Date')->date(),
            ];
        }

        // return $table
        //     ->columns(array_merge($commonColumns, $planColumns, $actualColumns));

        // Return the table with dynamic columns based on the active tabreturn $table
        return $table
            ->columns(match ($activeTab) {
                'plan' => array_merge($commonColumns, $planColumns),
                'actual' => array_merge($commonColumns, $actualColumns),
                default => array_merge($commonColumns, $planColumns, $actualColumns),
            })
            ->actions([
                Tables\Actions\EditAction::make(), // aktifkan edit
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public function updatedActiveTab($tab): void
    {
        request()->merge(['activeTab' => $tab]);
    }

    public static function form(Form $form): Form
    {
        $viewMode = request()->input('activeTab', 'plan'); // Ambil tab aktif

        $commonFields = [
            Forms\Components\TextInput::make('kode_po')->required(),
            Forms\Components\TextInput::make('supplier')->required(),
        ];

        $planFields = [
            Forms\Components\DatePicker::make('inv_date')->label('Plan Invoice Date'),
            Forms\Components\DatePicker::make('biel_date')->label('Plan B/L Date'),
            Forms\Components\DatePicker::make('piel_date')->label('Plan P/L Date'),
            Forms\Components\DatePicker::make('cod_date')->label('Plan COO Date'),
        ];

        $actualFields = [
            Forms\Components\DatePicker::make('inv_actdate')->label('Actual Invoice Date'),
            Forms\Components\DatePicker::make('biel_actdate')->label('Actual B/L Date'),
            Forms\Components\DatePicker::make('piel_actdate')->label('Actual P/L Date'),
            Forms\Components\DatePicker::make('cod_actdate')->label('Actual COO Date'),
        ];

        return $form->schema([
            ...$commonFields,
            ...($viewMode === 'plan' ? $planFields : $actualFields),
        ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => ManageDocuments::route('/'),
        ];
    }
}
