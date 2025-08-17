<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOutResource\Pages;
use App\Filament\Resources\StockOutResource\RelationManagers;
use App\Mail\ApprovedRequest;
use App\Models\Damaged;
use App\Models\Item;
use App\Models\MissingItems;
use App\Models\Stock;
use App\Models\StockOut;
use App\Models\StockOutDetails;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Notifications\RequestApprovalNotification;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Split;

class StockOutResource extends Resource
{
    protected static ?string $model = StockOut::class;

    protected static ?string $navigationLabel = 'Request ';
    protected static ?string $modelLabel = 'Request';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?int $navigationSort = 3;

//     public static function getNavigationBadge(): ?string
// {
//     $query = static::getModel()::where('approved', 0);
    
//     if (auth()->check() && auth()->user()->role === 'Inspector') {
//         $query->where('user_id', auth()->user()->id);
//     }
//     return $query->count();
// }

    protected static ?string $navigationBadgeTooltip = 'Active Requests';

    public static function getNavigationBadgeColor(): ?string
    {
        return  'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('wo_id')
                    ->required()
                    ->numeric()
                    ->label(__('Work Order ID')),
                Select::make('warehouse_id')
                    ->relationship('outWarehouse', 'name')
                    ->label(__("Warehouse"))
                    ->required()
                    ->native(0)
                    ->live(),
                Repeater::make('outDetails')
                    ->deletable(function ($record) {

                        if ($record?->approved) {
                            return auth()->user()->hasRole('super_admin');
                        }
                        return true;
                    })
                    ->addable(fn ($record) => !$record?->approved)
                    ->relationship()
                    ->label(__('Choose Items'))
                    ->addActionLabel('Add Items')
                    ->schema(components: [
                        Select::make('stock_id')
                            ->live()
                            ->searchable()
                            ->disabled(fn ($record) => $record?->stockOut->approved)
                            ->options(function (Get $get, string $operation) {
                                $selectedItemIds = collect($get('../'))
                                    ->pluck('stock_id')
                                    ->filter()
                                    ->toArray();

                                // Add this block to handle edit mode
                                if ($operation === 'edit') {
                                    $currentStockId = $get('stock_id');
                                    // Remove current record's ID from excluded IDs
                                    $selectedItemIds = array_diff($selectedItemIds, [$currentStockId]);
                                }

                                $warehouse_id = $get('../../warehouse_id');

                                return Stock::query()
                                    ->select('inv_stocks.*')
                                    ->join('inv_items', 'inv_stocks.item_id', '=', 'inv_items.id')
                                    ->where('warehouse_id', $warehouse_id)
                                    ->whereNotIn('inv_stocks.id', $selectedItemIds)
                                    ->with('item')
                                    ->orderBy('inv_items.name', 'asc')
                                    ->get()
                                    ->mapWithKeys(function ($stock) use ($operation) {
                                        if (!$stock->item) return [];

                                        // Simplified logic for edit mode
                                        if ($operation === 'edit' && $stock->item->status === 1 || ($operation === 'create' && $stock->item->status === 1)) {
                                            $size = in_array(strtolower($stock->item->size), ['n/a', 'na'])
                                                ? null
                                                : $stock->item->size;

                                            $label = $stock->item->name;
                                            if ($size) $label .= " - {$size}";
                                            if ($stock->item->serial_number) $label .= " S/N: {$stock->item->serial_number}";

                                            return [$stock->id => $label];
                                        }

                                        return [];
                                    });
                            })
                            ->optionsLimit(10)
                            ->label(__('Item Name'))
                            ->native(false)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $stockId = $get('stock_id');
                                $stock = $stockId ? Stock::find($stockId) : null;
                                $set('item_id', $stock?->item_id);
                            }),
                        TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->readOnly(fn ($record) => $record?->stockOut->approved)
                            ->numeric()
                            ->live()
                            ->required()
                            ->reactive()
                            ->maxValue(function (string $operation, ?Model $record,Get $get) {
                                if($operation == 'edit' && $record?->stockOut->approved) {
                                    return 10000;
                                }
                                $stockId = $get('stock_id');
                                if (!$stockId) {
                                    return null;
                                }
                                $stock = Stock::query()
                                    ->where('id', $stockId)
                                    ->first();
                                return $stock ? $stock->available_quantity : null;
                            })
                            ->suffix(function (Get $get) {
                                $stockId = $get('stock_id');
                                if (!$stockId) {
                                    return 'N/A';
                                }
                                $stock = Stock::query()
                                    ->where('id', $stockId)
                                    ->with('item')
                                    ->first();
                                return $stock && $stock->item
                                    ? 'In Stock: '.$stock->available_quantity
                                    : 'N/A';
                            })
                            ->live()
                        ,
                        TextInput::make('note')
                            ->label(__('Notes')),
                        Toggle::make('returned')
                            ->visible(fn ($record, string $operation) => auth()->user()->hasAnyRole(['admin', 'store_keeper']) && $record?->outItems->category->type->is_returned && $operation === 'edit' )
                            ->inline(false)
                            ->label(__('Returned')),
                     ])->mutateRelationshipDataBeforeCreateUsing(function (array $data, \Illuminate\Database\Eloquent\Model $record): array {
                        $data['item_id'] = Stock::find($data['stock_id'])->item_id;
                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data, \Illuminate\Database\Eloquent\Model $record): array {
                        $data['item_id'] = Stock::find($data['stock_id'])->item_id;
                        return $data;
                    })
                    ->columns(3)
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('wo_id')
                            ->label(__('Work Order ID')),
                        TextEntry::make('outWarehouse.name')
                            ->label(__('Warehouse')),
                        TextEntry::make('outUserRequested.full_name_en')
                            ->label(__('Requested By')),
                        TextEntry::make('request_date')
                            ->label(__('Requested Date')),
                        TextEntry::make('outUserApproved.full_name_en')
                            ->label(__('Approved By')),
                        TextEntry::make('approve_date')
                            ->label(__('Approved Date')),
                    ])->columns(6),
                RepeatableEntry::make('outDetails')
                    ->label('Items')
                    ->schema([
                        TextEntry::make('outItems.name')
                            ->label(__('Name')),
                        TextEntry::make('outItems.serial_number')
                            ->label(__('Serial Number')),
                        TextEntry::make('outItems.size')
                            ->label(__('Size')),
                        TextEntry::make('quantity')
                            ->label(__('Quantity')),
                        TextEntry::make('note')
                            ->label(__('Notes')),
                            IconEntry::make('returned')
                             ->label(__('Returned'))
                            ->boolean()
                            ->tooltip(fn ($state) => $state ? __('Item has been returned') : __('Item has not been returned'))
                            ->visible(fn ($record) => !$record->outItems->category->type->is_consumable)

                    ])
                    ->columns(5)
            ])->columns(1);

    }
    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->hasRole('Inspector'))
        {

            return parent::getEloquentQuery()
                ->when(auth()->user()->hasRole('Inspector'), function ($query) {
                    $query->where('request_by', auth()->id());

                });
        }
        return parent::getEloquentQuery()
            ->when(auth()->user()->hasAnyRole('admin', 'store_keeper'), function ($query) {

                $query->get();
            });


    }
    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(
                fn (Model $record): string => Pages\ViewStockOut::getUrl([$record->id]),)->columns([

                ColumnGroup::make('Request Info', [
                    Tables\Columns\TextColumn::make('id')
                        ->label('No.')
                        ->numeric()
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('wo_id')
                        ->label('Work Order')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('outWarehouse.name')
                        ->label('Warehouse')
                        ->numeric()
                        ->sortable()
                        ->searchable(),
                ]),
                ColumnGroup::make('Inspector', [

                    Tables\Columns\TextColumn::make('outUserRequested.full_name_en')
                        ->label('Name')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('request_date')
                        ->label('Date')
                        ->date()
                        ->sortable(),
                ]),


                ColumnGroup::make('Supervisor', [
                    Tables\Columns\IconColumn::make('supervisor_approve')
                        ->label('Status')
                        ->boolean(),
                    Tables\Columns\TextColumn::make('supervisor_approve_date')
                        ->label('Date')
                        ->date()
                        ->sortable(),
                ]),
                ColumnGroup::make('Store Keeper', [
                    Tables\Columns\IconColumn::make('approved')
                        ->label('Status')
                        ->boolean(),
                    Tables\Columns\TextColumn::make('approve_date')
                        ->label('Date')
                        ->date()
                        ->sortable(),
                ]),

                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle',
                        '2' => 'heroicon-o-exclamation-circle',
                        '3' => 'heroicon-o-shield-exclamation',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        '2' => 'warning',
                        '3' => 'light',
                        default => 'gray',
                    })->tooltip(fn (string $state): string => match ($state) {
                        '0' => 'Not Returned',
                        '1' => 'Returned',
                        '2' => 'Partially Returned',
                        '3' => 'Returned with Damaged Item',
                        default => 'gray',
                    })
                    ->hidden(auth()->user()->hasRole('Inspector'))
                    ->label(__('Returned')),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        '0' => 'Not Returned',
                        '1' => 'Returned',
                        '2' => 'Partially Returned',
                        '3' => 'Returned with Damaged Item',
                    ]),
            ])->defaultSort('created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper', 'Supervisor']) && !$record->status
                        ),


                    Action::make('approve_supervisor')
                        ->label(__('Approve'))
                        ->icon('heroicon-o-check-circle')
                        ->iconSize('lg')
                        ->action(function (StockOut $record) {
                            $record->update([
                                'supervisor_approve' => true,
                                'supervisor_id' => auth()->id(),
                                'supervisor_approve_date' => now(),
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading(__('Approve Request as Supervisor'))
                           ->modalSubheading(function ($record) {
                            return __('Are you sure you want to approve this Request :request_number? This action cannot be undone.', [
                                'request_number' => '#'.$record->id // Replace with your actual request number field
                            ]);
                        })
                        ->modalButton(__('Approve'))
                        ->color('success')
                        ->visible(fn ($record) => auth()->user()->hasAnyRole('Supervisor', 'admin') && !$record->supervisor_approve && !$record->approved),


                    //APPROVE THE REQUEST
                    Action::make('approve')
                        ->label(__('Approve'))
                        ->icon('heroicon-o-check-circle')
                        ->iconSize('lg')
                        ->action(function (StockOut $record) {

                            $details = $record->outDetails;
                            $containsReturnedItems = false;
                            foreach ($details as $detail) {
                                $stock = Stock::find($detail->stock_id);
                                $item = Item::find($stock->item_id);
                                if($stock->available_quantity < $detail->quantity) {
                                    Notification::make()
                                        ->title('Insufficient Stock')
                                        ->danger()
                                        ->body("The request includes items with insufficient quantities. Please review and adjust accordingly.")
                                        ->send();
                                    return;
                                }
                                elseif($stock->available_quantity == 0) {
                                    Notification::make()
                                        ->title('Insufficient Stock')
                                        ->danger()
                                        ->body("The request includes items with insufficient quantities. Please review and adjust accordingly.")
                                        ->send();
                                    return;
                                }

                                if (boolval($item->category->type->is_returned)) {
                                    $containsReturnedItems = true;
                                    $stock->update([
                                        'available_quantity' => $stock->available_quantity - $detail->quantity,
                                    ]);
                                    $item->update([
                                        'status' => 4,
                                    ]);

                                } elseif (boolval($item->category->type->is_consumable)) {
                                    $stock->update([
                                        'available_quantity' => $stock->available_quantity - $detail->quantity,
                                        'quantity' => $stock->quantity - $detail->quantity,
                                    ]);

                                    if($stock->quantity <= $item->low_stock){

                                        $users = \App\Models\User::role(['store_keeper'])->get();
                                        // Send notifications
                                        foreach ($users as $user) {
                                            $user->notify(new LowStockNotification($detail->outItems->name,$stock->quantity));
                                        }
                                    }
                                }
                            }

                            $record->update([
                                'approved' => true,
                                'approved_by' => auth()->id(),
                                'approve_date' => now(),
                                'status' => $containsReturnedItems ? 0 : 1,
                            ]);

                            $stockOut = $record;

                            // Fetch the user who made the request
                            $requestedBy = $stockOut->outUserRequested->full_name_en ?? 'Unknown User';

                            // Fetch the items in the request
                            $items = [];
                            foreach ($stockOut->outDetails as $detail) {
                                $items[] = [
                                    'name' => $detail->outItems->name ?? 'Unknown Item',
                                    'quantity' => $detail->quantity,
                                ];
                            }

                            // Prepare the request data
                            $requestData = [
                                'request_number' => $stockOut->id,
                                'requested_by' => $requestedBy, // Include requested_by
                                'wo_id' => $stockOut->wo_id, // Include wo_id
                                'items' => $items,
                            ];

                            // Notify the user who made the request
                            $user = User::find($stockOut->request_by);
                            $user->notify(new RequestApprovalNotification($requestData, 'approved'));
                        })
                        ->requiresConfirmation()
                        ->modalHeading(__('Approve Stock Out Request'))
                           ->modalSubheading(function ($record) {
                            return __('Are you sure you want to approve this Request :request_number? This action cannot be undone.', [
                                'request_number' => '#'.$record->id // Replace with your actual request number field
                            ]);
                        })
                        ->modalButton(__('Approve'))
                        ->color('success')
                        ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper'])&& !$record->approved && $record->supervisor_approve),

                    //RETURN THE ITEMS
                    Action::make('returned')
                        ->label(__('Return'))
                        ->icon('heroicon-o-arrow-path')
                        ->iconSize('lg')
                        ->form(function ($record) {
                            $details = $record->outDetails;

                            // Prepare options for CheckboxList
                            $options = [];
                            foreach ($details as $detail) {
                                if ($detail->outItems && !$detail->outItems->category->type->is_consumable && !$detail->returned) {
                                    $options[$detail->outItems->id] = $detail->outItems->name . ' ' . $detail->outItems->serial_number;
                                }
                            }

                            return [
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        0 => 'Not Returned',
                                        1 => 'Returned',
                                        2 => 'Partially Returned',
                                        3 => 'Have Damaged Items',
                                        4 => 'Have Missing Items',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->native(false),

                                DatePicker::make('return_date')
                                    ->label('Return Date')
                                    ->reactive()
                                    ->native(false),

                                CheckboxList::make('items')
                                    ->label('Items to Return')
                                    ->options($options)
                                    ->bulkToggleable(fn ($get) => $get('status') !== 1)
                                    ->hidden(fn ($get) => $get('status') == 1)
                                    ->reactive()
                                    ->live()
                                    ->required(fn ($get) => $get('status') !== 1),
                            ];
                        })
                        ->action(function (array $data, StockOut $record) {
                            if (in_array($record->status, [0, 2])) {
                                $selectedItems = $data['items'] ?? [];
                                $status = $data['status'];
                                $details = $record->outDetails;

                                foreach ($details as $detail) {
                                    $item = $detail->outItems;

                                    if (boolval($item->category->type->is_returned) && $item->status == '4') {
                                        if (in_array($detail->item_id, $selectedItems)) {
                                            // If the item is CHECKED, apply corresponding status
                                            if ($status == 4) {
                                                // Missing
                                                MissingItems::create([
                                                    'stock_out_id' => $record->id,
                                                    'missing_by' => $record->request_by,
                                                    'item_id' => $detail->item_id,
                                                    'stock_id' => $detail->stock_id,
                                                    'work_order_id' => $record->wo_id,
                                                    'reported_by' => auth()->id(),
                                                    'warehouse_id' => $record->warehouse_id,
                                                    'quantity' => $detail->quantity,
                                                    'status' => 0,
                                                    'notes' => 'The employee did not return the tool ' . $detail->outItems->name . ' for WO# ' . $record->wo_id,
                                                    'reported_at' => now(),
                                                ]);
                                                $item->update(['status' => 5]); // Mark as Missing

                                            } elseif ($status == 3) {
                                                // Damaged
                                                Damaged::create([
                                                    'stock_out_id' => $record->id,
                                                    'item_id' => $detail->item_id,
                                                    'stock_id' => $detail->stock_id,
                                                    'damaged_by' => $record->request_by,
                                                    'work_order_id' => $record->wo_id,
                                                    'warehouse_id' => $record->warehouse_id,
                                                    'resolved_by' => null,
                                                    'resolved_at' => null,
                                                    'description' => '',
                                                    'reported_by' => auth()->id(),
                                                    'reported_at' => now(),
                                                    'status' => '0',
                                                ]);
                                                $item->update(['status' => 7]); // Mark as Damaged

                                            } elseif ($status == 2) {
                                                // Partially Returned (Do nothing, just mark as partially returned)
                                                $record->update(['status' => 2]);
                                            }

                                        } else {
                                            // If the item is UNCHECKED, consider it RETURNED
                                            $detail->update(['returned' => true]);
                                            $stock = Stock::find($detail->stock_id);

                                            if ($stock) {
                                                $stock->increment('available_quantity'); // Increase stock
                                                $item->update(['status' => 1]); // Mark as Available
                                            }
                                        }
                                    }
                                }

                                // If status is not "Partially Returned," update it to "Returned"
                                if ($status != 2) {
                                    $record->update(['status' => $status, 'return_date' => now()]);
                                }
                            }
                        })
                        ->slideOver()
                        ->requiresConfirmation()
                        ->modalHeading(__('Mark Stock as Returned'))
                       ->modalSubheading(function ($record) {
                            return __('Are you sure you want to mark this request :request_number? as returned?  This action will update the stock quantities.', [
                                'request_number' => '#'.$record->id // Replace with your actual request number field
                            ]);
                        })
                        ->modalButton(__('Confirm Return'))
                        ->color('success')
                        ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper']) && in_array($record->status, [0, 2]) && $record->approved),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListStockOuts::route('/'),
            'create' => Pages\CreateStockOut::route('/create'),
            'edit' => Pages\EditStockOut::route('/{record}/edit'),
            'view' => Pages\ViewStockOut::route('/{record}'),
        ];
    }
}
