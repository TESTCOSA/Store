<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectorsResource\Pages;
use App\Models\UserDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InspectorsResource extends Resource
{
    protected static ?string $model = UserDetails::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Inspectors Tracking';
    protected static ?string $modelLabel = 'Inspectors Tracking';

    protected static ?string $navigationGroup = 'Assets';
    protected static ?int $navigationSort = 7;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Inspector Info')
                            ->icon('heroicon-o-user')
                            ->iconPosition(IconPosition::After)
                            ->schema([

                                TextEntry::make('emp_code')
                                    ->label('Employee Code'),
                                TextEntry::make('full_name_en')
                                    ->label('Name'),
                            ])->columns(2),
                        Tabs\Tab::make('Active Requests')
                            ->icon('heroicon-o-play-circle')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make('activeStockOut')
                                    ->label('Active Requests')
                                    ->schema([
                                        // Main stock-out information fields
                                        TextEntry::make('id')
                                            ->label('Request Number'),
                                        TextEntry::make('wo_id')
                                            ->label('Work Order Number'),
                                        TextEntry::make('outWarehouse.name')
                                            ->label('Warehouse Name'),
                                        TextEntry::make('outUserApproved.full_name_en')
                                            ->label('Approved By'),
                                        TextEntry::make('request_date')
                                            ->label('Request Date'),
                                        TextEntry::make('approve_date')
                                            ->label('Approved Date'),
                                        IconEntry::make('status')
                                                        ->icon(fn (string $state): string => match ($state) {
                                                            '0' => 'heroicon-o-x-circle',              // Not Returned
                                                            '1' => 'heroicon-o-check-circle',          // Returned
                                                            '2' => 'heroicon-o-exclamation-circle',    // Partially Returned
                                                            '3' => 'heroicon-o-shield-exclamation',    // Returned with Damaged Item
                                                            '4' => 'heroicon-o-exclamation-triangle',  // Returned with Missing Items
                                                            default => 'heroicon-o-question-mark-circle',
                                                        })
                                                        ->color(fn (string $state): string => match ($state) {
                                                            '0' => 'danger',
                                                            '1' => 'success',
                                                            '2' => 'warning',
                                                            '3' => 'light',
                                                            '4' => 'warning',
                                                            default => 'gray',
                                                        })
                                                        ->tooltip(fn (string $state): string => match ($state) {
                                                            '0' => 'Not Returned',
                                                            '1' => 'Returned',
                                                            '2' => 'Partially Returned',
                                                            '3' => 'Returned with Damaged Item',
                                                            '4' => 'Returned with Missing Items',
                                                            default => 'Unknown Status',
                                                        })
                                                        ->size('lg')         // Set icon size; adjust as needed ('sm', 'lg', 'xl', etc.)
                                                        ->alignment('center'),
                                        TextEntry::make('info_link')
                                            ->label('Info')
                                            ->url(fn ($record) => url("app/stock-outs/{$record->id}"))
                                            ->openUrlInNewTab()
                                            ->icon('heroicon-o-information-circle')
                                            ->color('info')
                                            ->default('View Info'),
                                    ])
                                    ->columns(8)
                                    ->columnSpanFull()
                                    ->contained(true)
                            ]),
                        Tabs\Tab::make('All Requests')
                            ->icon('heroicon-o-bars-4')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make('allStockOut')
                                    ->label('All Requests')
                                    ->schema([
                                        // Main stock-out information fields
                                        TextEntry::make('id')
                                            ->label('Request Number'),
                                        TextEntry::make('wo_id')
                                            ->label('Work Order Number'),
                                        TextEntry::make('outWarehouse.name')
                                            ->label('Warehouse Name'),
                                        TextEntry::make('outUserApproved.full_name_en')
                                            ->label('Approved By'),
                                        TextEntry::make('request_date')
                                            ->label('Request Date'),
                                        TextEntry::make('approve_date')
                                            ->label('Approved Date'),

                                        IconEntry::make('status')
                                            ->icon(fn (string $state): string => match ($state) {
                                                '1' => 'heroicon-o-check-circle',
                                                '2' => 'heroicon-o-exclamation-circle',
                                                default => 'heroicon-o-question-mark-circle',
                                            })
                                            ->color(fn (string $state): string => match ($state) {
                                                '1' => 'success',
                                                '2' => 'warning',
                                                default => 'gray',
                                            })
                                            ->tooltip(fn (string $state): string => match ($state) {
                                                '1' => 'Returned',
                                                '2' => 'Partially Returned ',
                                                default => 'Unknown State',
                                            })
                                            ->size('lg') // Adjust size if needed, e.g., 'sm', 'lg', 'xl'
                                            ->alignment('center'),
                                        TextEntry::make('info_link')
                                            ->label('Info')
                                            ->url(fn ($record) => url("app/stock-outs/{$record->id}"))
                                            ->openUrlInNewTab()
                                            ->icon('heroicon-o-information-circle')
                                            ->color('info')
                                            ->default('View Info'),
                                    ])
                                    ->columns(8)
                                    ->columnSpanFull()
                                    ->contained(true)
                            ]),
                        Tabs\Tab::make('Missing')
                            ->icon('heroicon-o-archive-box-x-mark')
                            ->iconPosition(IconPosition::After)
                            ->schema([

                                RepeatableEntry::make('missing')
                                    ->label('Missing')
                                    ->schema([
                                        TextEntry::make('stockOut.id')
                                            ->label('Request Number'),
                                        TextEntry::make('work_order_id')
                                            ->label('Work Order ID'),
                                        TextEntry::make('item.name')
                                            ->label('Item Name'),
                                        TextEntry::make('item.test_tag')
                                            ->label('Test Tag'),
                                        TextEntry::make('reported_at')
                                            ->label('Missing At'),
                                        TextEntry::make('resolved_at')
                                            ->label('Resolved At'),
                                        IconEntry::make('status')
                                            ->label('Resolved')
                                            ->boolean(),
                                        TextEntry::make('description')
                                            ->label('Notes'),
                                    ])->columns(7)

                            ])->columnSpanFull(),
                        Tabs\Tab::make('Damaged')
                            ->icon('heroicon-o-archive-box-x-mark')
                            ->iconPosition(IconPosition::After)
                            ->schema([

                                RepeatableEntry::make('damaged')
                                    ->label('Damaged')
                                    ->schema([
                                        TextEntry::make('stockOut.id')
                                            ->label('Request Number'),
                                        TextEntry::make('work_order_id')
                                            ->label('Work Order ID'),
                                        TextEntry::make('item.name')
                                            ->label('Item Name'),
                                        TextEntry::make('item.test_tag')
                                            ->label('Test Tag'),
                                        TextEntry::make('reported_at')
                                            ->label('Damaged At'),
                                        TextEntry::make('resolved_at')
                                            ->label('Resolved At'),
                                        IconEntry::make('status')
                                            ->label('Resolved')
                                            ->boolean(),
                                        TextEntry::make('description')
                                            ->label('Notes'),
                                    ])->columns(7)

                            ])->columnSpanFull(),
                    ]),


            ])->columns(1);

    }
    public static function getEloquentQuery(): Builder
    {

        if(auth()->user()->hasRole('Inspector'))
        {
            return parent::getEloquentQuery()
                ->whereHas('user', function ($query) {
                    $query->where('user_id', auth()->id()); // Filter users with group_id = 4
                });
        }
        if(auth()->user()->hasAnyRole('admin', 'store_keeper', 'HR')) {
            return parent::getEloquentQuery()
                ->whereHas('userGroups', function ($query) {
                    $query->where('group_id', 4); // Filter users with group_id = 4
                })
                ->whereHas('user', function ($query) {
                    $query->where('enabled', '=', '1'); // Include only enabled users
                });
        }
        return parent::getEloquentQuery();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([

                Tables\Columns\TextColumn::make('emp_code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name_ar')
                    ->label(__('Arabic Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name_en')
                    ->label(__('English Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                IconColumn::make('has_unresolved_missing_items')
                    ->label('Status') // Optional label
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([

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
            'index' => Pages\ListInspectors::route('/'),
            'create' => Pages\CreateInspectors::route('/create'),
            'view' => Pages\ViewInspectors::route('/{record}'),
        ];
    }
}
