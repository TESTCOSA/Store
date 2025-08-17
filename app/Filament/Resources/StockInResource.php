<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockInResource\Pages;
use App\Filament\Resources\StockInResource\RelationManagers;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockIn;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockInResource extends Resource
{
    protected static ?string $model = StockIn::class;

    protected static ?string $navigationLabel = 'Stock In';
    protected static ?string $modelLabel = 'Stock In';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Section::make()
                    ->schema([
                        Select::make('warehouse_id')
                            ->relationship('inWarehouse', 'name')
                            ->label(__("Warehouse"))
                            ->required()
                            ->native(false)
                            ->reactive(),
                    ])->columns(3),
                Repeater::make('inDetails')
                    ->collapsed()
                    ->cloneable()
                    ->label(__('Choose Items'))
                    ->addActionLabel('Add Items')
                    ->relationship('inDetails')
                    ->schema(components: [
                        Select::make('supplier_id')
                            ->relationship('inSupplier',
                                'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('status', 1),)
                            ->label(__("Supplier"))
                            ->required()
                            ->reactive()
                            ->native(false),
                        Select::make('item_id')
                            ->label(__('Item Name'))
                            ->reactive()
                            ->optionsLimit(5)
                            ->searchable()
                            ->native(false)
                            ->options(function (callable $get, string $operation) {
                                $selectedItemIds = collect($get('../'))
                                    ->pluck('item_id')
                                    ->filter()
                                    ->toArray();
                                return Item::query()
                                        ->where(function ($query) {
                                            $query->whereDoesntHave('stock')
                                            ->whereHas('category.type', function ($typeQuery) {
                                                $typeQuery->where('is_consumable', false);
                                            });
                                        })
                                        ->orWhere(function ($query) {
                                            $query
                                            ->whereHas('category.type', function ($typeQuery) {
                                                $typeQuery->where('is_consumable', true);
                                            });
                                        })
                                        ->whereNotIn('id', $selectedItemIds)
                                        ->get()
                                    ->mapWithKeys(function ($item) {
                                        if ($item?->category->type->is_returned) {
                                            return [$item->id => $item->name . '-' . $item->test_tag];
                                        }
                                        if ($item?->category->type->is_consumable) {
                                            return [$item->id => $item->name . '-' . $item->size];
                                        }
                                        return [$item->id => $item->name];
                                    })->toArray();
                            })
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $item = Item::find($state);

                                    if ($item && $item->category->type->is_returned) {
                                        $set('quantity', 1);
                                    }
                                }
                            })->formatStateUsing(function ($state) {

                                $item = Item::find($state);

                                if(empty($item)) {
                                    return 'Choose Item';
                                }
                                    return $item?->category->type->is_returned ? $item?->name . '-' . $item?->test_tag : $item?->name . '-' . $item?->size;
                            }),
                        TextInput::make('quantity')
                            ->numeric()
                            ->label(__('Quantity'))
                            ->minValue(1)
                            ->reactive()
                            ->live()
                            ->maxValue(function ($get){
                                if (!empty($get('item_id'))){
                                    $item = Item::find($get('item_id'));
                                    if ($item?->category->type->is_returned) {
                                        return 1;
                                    }
                                    return null;
                                }
                                return null;
                            }),
                    ])->columns(3)
                    ->columnSpanFull()
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, \Illuminate\Database\Eloquent\Model $record): array {
                        $data['stock_in_id'] = $record->id;
                        return $data;
                    })->mutateRelationshipDataBeforeSaveUsing(function (array $data, \Illuminate\Database\Eloquent\Model $record): array {
                        $data['stock_in_id'] = $record->id;
                        return $data;
                    })
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        TextEntry::make('inWarehouse.name')
                            ->label(__('Warehouse')),
                        TextEntry::make('inUserStocked.full_name_en')
                            ->label(__('Requested By')),
                        TextEntry::make('stocked_date')
                            ->label(__('Stocked In Date')),
                        TextEntry::make('inUserApproved.full_name_en')
                            ->label(__('Approved By')),
                        TextEntry::make('approve_date')
                            ->label(__('Approved Date')),
                    ])->columns(6),
                RepeatableEntry::make('inDetails')
                    ->schema([
                         TextEntry::make('inSupplier.name')
                            ->label(__('Supplay from')),
                        TextEntry::make('inItems.name')
                            ->label(__('Item Name')),
                        TextEntry::make('inItems.serial_number')
                            ->label(__('Item S/N')),
                        TextEntry::make('quantity')
                            ->label(__('Quantity')),
                        TextEntry::make('note')
                            ->label(__('Notes')),
                        IconEntry::make('inItems.category.type.is_consumable')
                            ->label(__('Consumable'))
                            ->boolean(),
                    ])
                    ->columns(6)
            ])->columns(1);

    }
    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(
            fn (Model $record): string => Pages\ViewStockIn::getUrl([$record->id]),
        )
            ->columns([
                Tables\Columns\TextColumn::make('inWarehouse.name')
                    ->label('Warehouse')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inUserStocked.full_name_en')
                    ->label('Restocked By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stocked_date')
                    ->label('Stocked In')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inUserApproved.full_name_en')
                    ->label('Approved By')
                    ->searchable(),
                Tables\Columns\IconColumn::make('approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('approve_date')
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
            ]) ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                //Approve
                Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check-circle')
                    ->iconSize('lg')
                    ->action(function ($record) {
                        $details = $record->inDetails;

                        foreach ($details as $detail) {

                            $stock = Stock::where('item_id',$detail->item_id)->where('warehouse_id',$record->warehouse_id)->first();
                            if(boolval($stock)) {
                                $detail->update([
                                    'status' => 1,
                                ]);
                                    $stock->update([
                                        'available_quantity' => $stock->available_quantity + $detail->quantity,
                                        'quantity' => $stock->quantity + $detail->quantity,
                                    ]);
                            }else
                            {
                               $newStock = Stock::create([
                                    'item_id' => $detail->item_id,
                                    'quantity' => $detail->quantity,
                                    'available_quantity' => $detail->quantity,
                                    'warehouse_id' => $record->warehouse_id,
                                ]);
                                $detail->update([
                                    'stock_id' => $newStock->id,
                                ]);
                            }
                        }
                        $record->update([
                            'approved' => true,
                            'approved_by' => auth()->id(),
                            'approve_date' => now(),
                            'status' => 1,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('Approve Stock In'))
                    ->modalSubheading(__('Are you sure you want to approve this Stock In request? This action cannot be undone.'))
                    ->modalButton(__('Approve'))
                    ->color('success')
                    ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper'])&& !$record->status),

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
            'index' => Pages\ListStockIns::route('/'),
            'create' => Pages\CreateStockIn::route('/create'),
            'edit' => Pages\EditStockIn::route('/{record}/edit'),
            'view' => Pages\ViewStockIn::route('/{record}'),
        ];
    }
}
