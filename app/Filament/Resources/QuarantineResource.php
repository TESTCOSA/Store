<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuarantineResource\Pages;
use App\Filament\Resources\QuarantineResource\RelationManagers;
use App\Models\Item;
use App\Models\Quarantine;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuarantineResource extends Resource
{
    protected static ?string $model = Quarantine::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Quarantines';
    protected static ?string $modelLabel = 'Quarantines';
    protected static ?string $navigationGroup = 'Assets';
    protected static ?int $navigationSort = 4;

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
                        TextEntry::make('item.name')
                            ->label('Item Name'),
                        TextEntry::make('item.test_tag')
                            ->label('Test Tag'),
                        TextEntry::make('item.serial_number')
                            ->label('Item S/N'),
                        TextEntry::make('user.full_name_en')
                            ->label('Quarantined By'),
                        TextEntry::make('quarantined_at')
                            ->date()
                            ->label('Quarantined At'),
                        TextEntry::make('released_at')
                        ->date()
                            ->label('Released At'),
                        TextEntry::make('releasedBy.full_name_en')
                            ->date()
                            ->label('Released By'),
                        IconEntry::make('status')
                                ->label('Status')
                           ->boolean(),
                        TextEntry::make('reason')
                            ->label(__('Reason'))
                            ->columnSpan(8)

                    ])->columns(8),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(fn (Model $record): string => Pages\ViewQuarantines::getUrl([$record->id]),)
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item Name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.test_tag')
                    ->label('Test Tag')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.full_name_en')
                    ->label('Quarantined By')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quarantined_at')
                    ->label('Quarantined At')
                    ->sortable(),
                Tables\Columns\TextColumn::make('released_at')
                    ->label('Released At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('releasedBy.full_name_en')
                    ->label('Released By')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                ,
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
                Action::make('approve')
                    ->label(__(''))
                    ->icon('heroicon-o-check-circle')
                    ->iconSize('lg')
                    ->action(function ($record) {

                        $item = Stock::find($record->stock_id)->item;
                        $item->update([
                            'status' => 1,
                        ]);
                        $record->update([
                            'approved' => true,
                            'released_by' => auth()->id(),
                            'released_at' => now(),
                            'status' => 1,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('Release Item'))
                    ->modalSubheading(__('Are you sure you want to release This Item.'))
                    ->modalButton(__('Release'))
                    ->color('success')
                    ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'store_keeper'])&& !$record->status),
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
            'index' => Pages\ListQuarantines::route('/'),
            'create' => Pages\CreateQuarantine::route('/create'),
            'edit' => Pages\EditQuarantine::route('/{record}/edit'),
            'view' => Pages\ViewQuarantines::route('/{record}'),
        ];
    }
}
