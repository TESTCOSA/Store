<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Stock;
use App\Models\StockOutDetails;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;
    protected static ?string $navigationLabel = 'Stock';
    protected static ?string $modelLabel = 'Stock';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->label(__("Item"))

                    ->required()
                    ->native(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('available_quantity')
                    ->required()
                    ->numeric(),
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label(__("Warehouse"))
                    ->required()
                    ->native(),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Stock Info')
                            ->icon('heroicon-o-circle-stack')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                TextEntry::make('item.name')
                                    ->label('Name'),
                                TextEntry::make('item.test_tag')
                                    ->label('Test Tag'),
                                     TextEntry::make('item.size')
                                    ->label('Size'),
                                TextEntry::make('quantity')
                                    ->label('Quantity'),
                                TextEntry::make('available_quantity')
                                    ->label('Available Quantity'),
                                TextEntry::make('warehouse.name')
                                    ->label('Warehouse'),
                            ])->columns(5),
                        Tabs\Tab::make('Restocks')
                            ->icon('heroicon-o-arrow-right-end-on-rectangle')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make('stockIns')
                                    ->label('Restocks')
                                    ->schema([
                                        TextEntry::make('inSupplier.name')
                                        ->label('Supplier'),
                                        TextEntry::make('stockIn.inUserStocked.full_name_en')
                                        ->label('Stocked By'),
                                        TextEntry::make('stockIn.stocked_date')
                                        ->label('Stocked Date'),
                                        TextEntry::make('stockIn.inUserApproved.full_name_en')
                                        ->label('Approved By'),
                                        TextEntry::make('stockIn.approve_date')
                                        ->label('Approved Date'),
                                        IconEntry::make('stockIn.approved')
                                            ->label('Approved')
                                            ->boolean(),
                                        TextEntry::make('quantity')
                                        ->label('Quantity'),
                                    ])->columns(7)
                                    ->columnSpanFull()
                                    ->contained(true)
                            ])->columns(4),
                        Tabs\Tab::make('Requests')
                            ->icon('heroicon-o-arrow-right-start-on-rectangle')
                            ->iconPosition(IconPosition::After)
                            ->schema([

                                RepeatableEntry::make('stockOuts')
                                    ->label('Requests')
                                    ->schema([
                                        TextEntry::make('stockOut.wo_id')
                                        ->label('Work Order'),
                                        TextEntry::make('stockOut.outUserRequested.full_name_en')
                                        ->label('Requested By'),
                                        TextEntry::make('stockOut.outUserApproved.full_name_en')
                                        ->label('Approved By'),
                                        TextEntry::make('stockOut.request_date')
                                        ->label('Request Date'),
                                        TextEntry::make('stockOut.approve_date')
                                        ->label('Approve Date'),
                                        TextEntry::make('quantity')
                                        ->label('Quantity'),
                                        IconEntry::make('stockOut.status')
                                            ->label('Returned')
                                            ->boolean(),
                                        IconEntry::make('returned')
                                            ->label('Returned')
                                            ->boolean(),
                                        TextEntry::make('stockOut.return_date')
                                            ->label('Return Date'),
                                    ])->columns(9)

                            ])->columnSpanFull(),
                    ]),


            ])->columns(1);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(
                fn (Model $record): string => Pages\ViewStock::getUrl([$record->id]),
            )
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                     Tables\Columns\TextColumn::make('item.size')
                     ->label('Size'),
                Tables\Columns\TextColumn::make('item.test_tag')
                    ->label('Test Tag')
                    ->copyable()
                    ->copyMessage('Test Tag copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('item.serial_number')
                    ->label('S/N')
                    ->numeric()
                    ->copyable()
                    ->copyMessage('S/N copied')
                    ->copyMessageDuration(1500)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('calibration')
                    ->label('Calibration')
                    ->icon('heroicon-o-scale')
                    ->getStateUsing(function ($record) {
                        // Compute a simple state based on whether calibration exists
                        return $record->item && $record->item->calibration ? 'view' : 'create';
                    })
                    ->url(function ($record) {
                        if($record->item->calibrations) {
                            return route('filament.app.resources.calibrations.view', $record->item->calibrations?->id);
                        }
                        return route('filament.app.resources.calibrations.create');
                    }, true)
                    ->tooltip(function ($record) {
                        if($record->item->calibrations) {
                            return 'View Calibration';
                        }
                        return 'Create Calibration';
                    })
                    ->color(function ($record) {
                        if($record->item->calibrations) {
                            return 'success';
                        }
                        return 'danger';
                    }),

                Tables\Columns\IconColumn::make('item.status')
                    ->sortable()
                    ->icon(fn (string $state): string => match ($state) {
                        '1' => 'heroicon-o-check-circle',
                        '2' => 'heroicon-o-wrench-screwdriver',
                        '3' => 'heroicon-o-scale',
                        '4' => 'heroicon-o-minus-circle',
                        '5' => 'heroicon-o-exclamation-circle',
                        '6' => 'heroicon-o-exclamation-triangle',
                        '7' => 'heroicon-o-information-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '2' => 'secondary',
                        '3' => 'info',
                        '4' => 'warning',
                        '5' => 'light',
                        '6' => 'dark',
                        '7' => 'danger',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        '1' => 'Available',
                        '2' => 'In Maintenance',
                        '3' => 'In Calibration',
                        '4' => 'In Use',
                        '5' => 'Missing',
                        '6' => 'Quarantined',
                        '7' => 'Damaged',
                    }),
                Tables\Columns\TextColumn::make('warehouse.name')
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
                Tables\Filters\SelectFilter::make('warehouse')
                    ->label(__('Warehouse'))
                    ->relationship('warehouse', 'name')
                    ->placeholder(__('All Warehouses')),
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Warehouse'))
                    ->relationship('item.category.type', 'name')
                    ->placeholder(__('Type')),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
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
            'index' => Pages\ListStocks::route('/'),
            'view' => Pages\ViewStock::route('/{record}'),
            'edit' => Pages\EditStock::route('/{record}/edit')
        ];
    }
}
