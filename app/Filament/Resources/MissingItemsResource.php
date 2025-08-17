<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MissingItemsResource\Pages;
use App\Filament\Resources\MissingItemsResource\RelationManagers;
use App\Mail\ApprovedRequest;
use App\Models\Item;
use App\Models\MissingItems;
use App\Models\Stock;
use App\Models\StockOut;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class MissingItemsResource extends Resource
{
    protected static ?string $model = MissingItems::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?string $navigationLabel = 'Missing Items';
    protected static ?string $modelLabel = 'Missing Items';
    protected static ?string $navigationGroup = 'Assets';
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
                Select::make('stock_id')
                    ->disabled(fn ($record) => $record?->calibrationOut?->approved)
                    ->options(
                        function (Get $get, string $operation) {
                            $warehouse_id = $get('warehouse_id');
                            return Stock::query()
                                ->where('warehouse_id', $warehouse_id)
                                ->with('item')
                                ->get()
                                 ->mapWithKeys(function ($stock) use ($operation) {
                                    if($stock->item->status === 1 ) {
                                        return [$stock->id => $stock->item->name . '-' . $stock->item->serial_number] ?? 'N/A';
                                    }elseif($operation === 'edit') {
                                        return [$stock->id => $stock->item->name . '-' . $stock->item->serial_number] ?? 'N/A';
                                    }else {
                                        return [];
                                    }
                                });
                        }
                    )
                    ->label(__('Item Name'))
                    ->optionsLimit(0)
                    ->required()
                    ->native(false),
                Forms\Components\Textarea::make('reason')
                    ->columnSpanFull(),

            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {

        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Split::make([
                            Section::make('Request Details')
                                ->description('The request details for the Missing items')
                                ->schema([
                                    TextEntry::make('missingBy.full_name_en')
                                        ->label(__('Missed By')),
                                    TextEntry::make('warehouse.name')
                                        ->label(__('Warehouse')),
                                    TextEntry::make('stockOut.wo_id')
                                        ->label(__('Work Order ID')),
                                    TextEntry::make('StockOut.request_date')
                                        ->label(__('Requested Date')),
                                    TextEntry::make('stockOut.outUserApproved.full_name_en')
                                        ->label(__('Approved By')),
                                    TextEntry::make('stockOut.approve_date')
                                        ->label(__('Approved Date')),
                                ])->columns(6),
                            Section::make([
                                TextEntry::make('item.name')
                                    ->label(__('Item Name')),
                                TextEntry::make('item.test_tag')
                                    ->label(__('Test Tag')),
                                TextEntry::make('item.serial_number')
                                    ->label(__('Serial Number')),
                                TextEntry::make('item.serial_number')
                                    ->label(__('Serial Number'))
                                    ->columnSpan(2),
                            ])
                                ->grow(false)
                                ->columns(2),
                        ]),
                        Section::make('damaged Reports Details')
                            ->description('Details of the reported damaged items.')
                            ->schema([
                                TextEntry::make('reporter.full_name_en')
                                    ->label(__('Reporter')),
                                TextEntry::make('reported_at')
                                    ->label(__('Reported Date')),
                                IconEntry::make('status')
                                    ->label(__('Status'))
                                    ->icon(fn (string $state): string => match ($state) {
                                        '0' => 'heroicon-o-clock',
                                        '1' => 'heroicon-o-check-circle',
                                        default => 'heroicon-o-question-mark-circle',
                                    })
                                    ->color(fn (string $state): string => match ($state) {

                                        '0' => 'warning',
                                        '1' => 'success',
                                        default => 'gray',
                                    })
                                    ->tooltip(fn (string $state): string => match ($state) {
                                        '0' => 'Not Resolved',
                                        '1' => 'Resolved',
                                        default => 'Unknown State',
                                    })
                                    ->size('lg'),
                                TextEntry::make('description')
                                    ->label(__('Description'))
                                    ->columnSpan(3),
                            ])->columns(3),
                    ]),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(
                fn (Model $record): string => Pages\ViewMissingItems::getUrl([$record->id]),
            )
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item Name')
                    ->numeric()
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('item.test_tag')
                    ->label('Test Tag')
                    ->numeric()
                    ->sortable()->searchable(),  
                    Tables\Columns\TextColumn::make('item.serial_number')
                    ->label('Serial Number')
                    ->numeric()
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('reported_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resolved_by')
                    ->numeric(),
                Tables\Columns\TextColumn::make('resolved_at')
                    ->dateTime(),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-clock',
                        '1' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {

                        '0' => 'warning',
                        '1' => 'success',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        '0' => 'Not Resolved',
                        '1' => 'Resolved',
                        default => 'Unknown State',
                    })
                    ->label(__('Status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //APPROVE THE REQUEST
                Action::make('approve')
                    ->label(__(''))
                    ->icon('heroicon-o-check-circle')
                    ->iconSize('lg')
                    ->form(function () {
                        return [
                            TextInput::make('notes')
                                ->label('Notes'),
                            ];
                    })
                    ->action(function (MissingItems $record) {
                        $stock = $record->stock;
                        $item = $record->item;
                        if ($record->status == 0) {
                            if($item->available_quantity == 0) {
                                $stock->update([
                                    'available_quantity' => $item->available_quantity + 1
                                ]);
                                $item->update([
                                    'status' => 1
                                ]);
                            }
                            $record->update([
                                'resolved_by' => auth()->id(),
                                'resolved_at' => now(),
                                'status' => 1,
                            ]);
                        }
                    })
                    ->slideOver()->requiresConfirmation()
                    ->modalHeading(__('Approve Stock Out Request'))
                    ->modalSubheading(__('Are you sure you want to approve this stock out request? This action cannot be undone.'))
                    ->modalButton(__('Approve'))
                    ->color('success')
                    ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper'])&& !$record->status),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMissingItems::route('/'),
            'create' => Pages\CreateMissingItems::route('/create'),
            'edit' => Pages\EditMissingItems::route('/{record}/edit'),
            'view' => Pages\ViewMissingItems::route('/{record}'),
        ];
    }
}
