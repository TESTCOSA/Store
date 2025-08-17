<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ItemExporter;
use App\Filament\Imports\ItemImporter;
use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use App\Tables\Columns\TestTag;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationLabel = 'Items';
    protected static ?string $modelLabel = 'Items';
    protected static ?string $navigationGroup = 'Assets';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label(__("Category"))
                    ->required()
                    ->optionsLimit(0)
//                    ->searchable(true)
                    ->native(false),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('size')
                    ->maxLength(255),
                Forms\Components\TextInput::make('serial_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('make')
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->maxLength(255),
                Select::make('status')
                    ->required()
                    ->default(1)
                    ->options([
                        1 => 'Available',
                        2 => 'In Maintenance',
                        3 => 'Calibration',
                        4 => 'In Use',
                        5 => 'Missing',
                        6 => 'Quarantine',
                        7 => 'Damaged',
                    ])
                    ->label('Status')
                    ->native(false),
                Forms\Components\TextInput::make('low_stock')
                    ->required()
                    ->numeric()
                    ->default(1),
                FileUpload::make('file')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory(function ($record){

                        return 'Items/'.$record->category->name;
                    })
                    ->disk('public')
                    ->downloadable()

            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('size'),
                        TextEntry::make('serial_number'),
                        TextEntry::make('make'),
                        TextEntry::make('model'),
                        TextEntry::make('sequence'),
                        TextEntry::make('low_stock'),
                        IconEntry::make('status')
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
                            })
                            ->size('lg') // Adjust size if needed, e.g., 'sm', 'lg', 'xl'
                            ->alignment('center')
                    ])->columns(8),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(
                fn (Model $record): string => Pages\ViewItems::getUrl([$record->id]),
            )->headerActions([

                Action::make('Update Tag')
                    ->color('success')
                    ->action(function () {
                        $company = env('SLUG', 'TEST');
                        $existingTags = Item::pluck('test_tag')->flip()->toArray();
                        $consumableGroups = [];
                        $returnableSequences = []; // Track sequences per returnable category

                        $items = Item::with(['category.type', 'category'])->get();

                        foreach ($items as $item) {
                            $category = $item->category;
                            $categorySlug = strtoupper($category->slug ?? 'N/A');
                            $isReturned = $category->type->is_returned ?? false;

                            if ($isReturned) {
                                $categoryId = $category->id;

                                // Initialize sequence for the category if not set
                                if (!isset($returnableSequences[$categoryId])) {
                                    $maxSequence = 0;

                                    // Find the maximum existing sequence in this category
                                    foreach ($existingTags as $tag => $value) {
                                        if (strpos($tag, "{$company}-{$categorySlug}-") === 0) {
                                            $parts = explode('-', $tag);
                                            $sequencePart = $parts[2] ?? '';

                                            // Extract the base sequence (ignore suffixes)
                                            $sequencePart = explode('-', $sequencePart)[0];
                                            if (is_numeric($sequencePart)) {
                                                $currentSeq = (int)$sequencePart;
                                                $maxSequence = max($maxSequence, $currentSeq);
                                            }
                                        }
                                    }

                                    // Start from the next sequence
                                    $returnableSequences[$categoryId] = $maxSequence + 1;
                                }

                                // Get and increment the sequence
                                $sequenceNumber = $returnableSequences[$categoryId]++;
                                $sequence = str_pad($sequenceNumber, 2, '0', STR_PAD_LEFT);
                                $baseTag = "{$company}-{$categorySlug}-{$sequence}";
                                $testTag = $baseTag;
                                $counter = 1;

                                // Ensure uniqueness
                                while (isset($existingTags[$testTag])) {
                                    $testTag = "{$baseTag}-" . str_pad($counter, 2, '0', STR_PAD_LEFT);
                                    $counter++;
                                }

                                $item->test_tag = $testTag;
                                $existingTags[$testTag] = true;
                                $item->save();
                            } else {
                                // Handle consumables (existing code)
                                $abbrev = strtoupper(substr(trim($item->name), 0, 2)) ?: 'XX';
                                $groupKey = $category->id . '|' . $abbrev;

                                if (!isset($consumableGroups[$groupKey])) {
                                    $consumableGroups[$groupKey] = 1;
                                } else {
                                    $consumableGroups[$groupKey]++;
                                }

                                $sequence = str_pad($consumableGroups[$groupKey], 2, '0', STR_PAD_LEFT);
                                $baseTag = "{$company}-{$categorySlug}-{$abbrev}-{$sequence}";
                                $testTag = $baseTag;
                                $suffix = 1;

                                while (isset($existingTags[$testTag])) {
                                    $testTag = "{$baseTag}-" . str_pad($suffix, 2, '0', STR_PAD_LEFT);
                                    $suffix++;
                                }

                                $item->test_tag = $testTag;

                                $existingTags[$testTag] = true;
                                $item->save();
                            }
                        }
                    })
            ])
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('test_tag')
                    ->searchable(),
                Tables\Columns\TextColumn::make('make')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('low_stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
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
                ExportBulkAction::make()
            ]);
    }
    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
            'view' => Pages\ViewItems::route('/{record}'),
        ];
    }
}
