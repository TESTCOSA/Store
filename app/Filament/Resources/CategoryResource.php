<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationLabel = 'Category';
    protected static ?string $modelLabel = 'Category';
    protected static ?string $navigationGroup = 'Assets';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('types_id')
                    ->relationship('type', 'name')
                    ->label(__("Type"))
                    ->required()
                    ->native(false),
                Forms\Components\Toggle::make('enabled')
                    ->required(),
                Forms\Components\TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(function () {
                        $highestSort = Category::max('sort'); // Replace YourModel with your actual model
                        return $highestSort + 1;
                    }),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('type.name'),
                IconEntry::make('enabled')->boolean(),
                IconEntry::make('type.is_calibrated')->boolean()
                ->label('Calibration Required'),
                IconEntry::make('type.is_returned')->boolean()
                    ->label('Returnable'),
                IconEntry::make('type.is_consumable')->boolean()
                    ->label('Consumable'),

                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name'),
                                TextEntry::make('size')
                                    ->label('Size'),
                                TextEntry::make('stock.quantity')
                                    ->label('Quantity'),
                                IconEntry::make('status')
                                    ->icon(fn (string $state): string => match ($state) {
                                        '1' => 'heroicon-o-check-circle',
                                        '2' => 'heroicon-o-wrench-screwdriver',
                                        '3' => 'heroicon-o-scale',
                                        '4' => 'heroicon-o-minus-circle',
                                        '5' => 'heroicon-o-exclamation-circle',
                                        '6' => 'heroicon-o-exclamation-triangle',
                                        default => 'heroicon-o-question-mark-circle',
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        '1' => 'success',
                                        '2' => 'secondary',
                                        '3' => 'info',
                                        '4' => 'warning',
                                        '5' => 'danger',
                                        '6' => 'dark',
                                        default => 'gray',
                                    })
                                    ->tooltip(fn (string $state): string => match ($state) {
                                        '1' => 'Available',
                                        '2' => 'In Maintenance',
                                        '3' => 'In Calibration',
                                        '4' => 'In Use',
                                        '5' => 'Missing',
                                        '6' => 'Quarantined',
                                        default => 'Unknown State',
                                    })
                                    ->size('xl') // Adjust size if needed, e.g., 'sm', 'lg', 'xl'
                                    ->alignment('center')
                            ])
                            ->columnSpanFull()
                            ->columns(4)
                            ->grid(3),
            ])->columns(3);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(
            fn (Model $record): string => Pages\ViewCategory::getUrl([$record->id]),
        )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort')
                    ->numeric()
                    ->sortable(), Tables\Columns\TextColumn::make('items_count') // Use the relationship count column
                ->label('Items Count')->counts('items'),
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
                ExportBulkAction::make(),
            ])->reorderable('sort')->defaultSort('sort');
    }
    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
            'view' => Pages\ViewCategory::route('/{record}'),
        ];
    }
}
