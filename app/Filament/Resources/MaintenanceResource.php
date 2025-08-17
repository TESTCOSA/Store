<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Filament\Resources\MaintenanceResource\RelationManagers;
use App\Models\Maintenance;
use App\Models\Item;
use App\Models\Quarantine;
use App\Models\Stock;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationLabel = 'Maintenance';
    protected static ?string $modelLabel = 'Maintenance';
    protected static ?string $navigationGroup = 'Calibration & Maintenance';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('warehouse_id')
                    ->relationship('warehouse','name')
                    ->label(__("Warehouse"))
                    ->required()
                    ->native(false),
                Select::make('supplier_id')
                    ->relationship('suppliers',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('status', 1))
                    ->label(__("Supplier"))
                    ->required()
                    ->native(false),
                Repeater::make('details')
                    ->collapsed()
                    ->cloneable()
                    ->label(__('Choose Items'))
                    ->addActionLabel('Add Items')
                    ->relationship('details')
                    ->schema(components: [
                        Select::make('stock_id')
                            ->optionsLimit(0)
                            ->disabled(fn ($record) => $record?->maintenance->approved)
                            ->options(
                                function (Get $get, string $operation) {
                                    $warehouse_id = $get('../../warehouse_id');
                                    return Stock::query()
                                        ->where('warehouse_id', $warehouse_id)
                                        ->with('item')
                                        ->get()
                                        ->mapWithKeys(function ($stock) use ($operation) {
                                            if($stock->item->status === 1 && $stock->item->category->type->is_calibrated) {
                                                return [$stock->id => $stock->item->name . '-' . $stock->item->test_tag] ?? 'N/A';
                                            }elseif($operation === 'edit' && $stock->item->category->type->is_calibrated ) {
                                                return [$stock->id => $stock->item->name . '-' . $stock->item->test_tag] ?? 'N/A';
                                            }else {
                                                return [];
                                            }
                                        });
                                }
                            )
                            ->label(__('Item Name'))
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $stockId = $get('stock_id');
                                if ($stockId) {
                                    $stock = Stock::find($stockId);
                                    $set('item_id', $stock->item_id);
                                } else {
                                    $set('item_id', null);
                                }
                            })
                            ->native(false),

                        FileUpload::make('file')
                            ->label(__('File'))
                            ->directory('Maintenance')
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

                    ])->columns(5)
                    ->columnSpanFull()
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, \Illuminate\Database\Eloquent\Model $record): array {
                        $data['item_id'] = Stock::find($data['stock_id'])->item_id;
                        $data['maintenance_id'] = $record->id;
                        return $data;
                    })
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maintenance_stock_out_date')
                    ->label('Requested At')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approvedBy.full_name_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approve_date')
                    ->label('Approved At')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Returned At')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
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
                Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Details')
                    ->modalWidth('3xl')
                    ->modalContent(function ($record) {
                        return view('tables.modals.details', ['details' => $record->details]);
                    }),
                Tables\Actions\EditAction::make(),
                //APPROVE THE REQUEST
                Tables\Actions\Action::make('approve')
                    ->label(__(''))
                    ->icon('heroicon-o-check-circle')
                    ->iconSize('lg')
                    ->action(function (Maintenance $record) {
                        $details = $record->details;
                        foreach ($details as $detail) {
                            $stock = Stock::find($detail->stock_id);

                            $item = Item::find($stock->item_id);

                            if (!boolval($record->approved)) {
                                $stock->update([
                                    'available_quantity' => $stock->available_quantity - 1,
                                ]);
                                $item->update([
                                    'status' => 2,
                                ]);
                            }
                        }
                        $record->update([
                            'approved' => 1,
                            'approved_by' => auth()->id(),
                            'approve_date' => now(),
                            'status' => 0,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('Approve Maintenance Out Request'))
                    ->modalSubheading(__('Are you sure you want to approve this Maintenance out request? This action cannot be undone.'))
                    ->modalButton(__('Approve'))
                    ->color('success')
                    ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper'])&& !$record->approved),
                //RETURN THE ITEMS
                Action::make('returned')
                    ->label(__('Return'))
                    ->icon('heroicon-o-arrow-path')
                    ->iconSize('lg')
                    ->form(function ($record) {
                        $details = $record->details;
                        // Prepare options for CheckboxList
                        $options = [];
                        $description = [];
                        foreach ($details as $detail) {
                            if ($detail->items) {
                                $options[$detail->item_id] = $detail->items->name.'-'.$detail->items->test_tag;
                                $description[$detail->item_id] = 'Check if the Maintainance is valid';
                            }
                        }
                        return [
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    0 => 'Not Valid Maintenance',
                                    1 => 'Valid Maintenance',
                                    2 => 'Partially Valid Maintenance',
                                ])
                                ->required()
                                ->native(false)
                                ->reactive(),
                            DatePicker::make('return_date')
                                ->label('Return Date')
                                ->native(false),
                            CheckboxList::make('items')
                                ->label('Items to Return')
                                ->options($options)
                                ->descriptions($description)
                                ->reactive()
                                ->live(),
                        ];
                    })->action(function (array $data, Maintenance $record) {

                        $items = $data['items'] ?? [];

                        $details = $record->details;

                        foreach ($details as $detail) {

                            $item = $detail->items;
                            $stock = $detail->stock;

                            if (in_array($detail->item_id, $items)){

                                if($stock->available_quantity == 0){
                                    $stock->update([
                                        'available_quantity' => $stock->available_quantity + 1,
                                    ]);
                                    $item->update(['status' => 1]);
                                }


                            } else{
                                // Item is NOT in the array; mark as NOT returned
                                Quarantine::create([
                                    'stock_id' => $detail->stock_id,
                                    'user_id' => auth()->id(),
                                    'reason' => 'Unable to maintain the item.',
                                    'status' => 0,
                                    'quarantined_at' =>now(),
                                    'released_at' => null,
                                ]);

                                $item->update(['status' => 6]);
                            }

                        }

                        $record->update(['status' => $data['status'], 'return_date' => $data['return_date']]);

                    })->slideOver()->requiresConfirmation()
                    ->modalHeading(__('Mark Items as Returned and Maintained'))
                    ->modalSubheading(__('Are you sure you want to mark this Items as returned? And Maintained? This action will update the stock quantities.'))
                    ->modalButton(__('Confirm Return'))
                    ->color('success')
                    ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper'])&& !$record->status && $record->approved),
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
            'index' => Pages\ListMaintenances::route('/'),
            'create' => Pages\CreateMaintenance::route('/create'),
            'edit' => Pages\EditMaintenance::route('/{record}/edit'),
        ];
    }
}
