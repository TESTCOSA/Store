<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalibrationResource\Pages;
use App\Filament\Resources\CalibrationResource\RelationManagers;
use App\Models\Calibration;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Actions\Action;

use Illuminate\Support\Facades\Storage;

class CalibrationResource extends Resource
{
    protected static ?string $model = Calibration::class;
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Calibration';
    protected static ?string $modelLabel = 'Calibration';
    protected static ?string $navigationGroup = 'Assets';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                    ->options(
                        function () {
                            return Item::query()
                                ->get()
                                ->mapWithKeys(function ($items){
                                    if ($items->category->type->is_calibrated){
                                        return [$items->id => $items->name . ' ' . $items->test_tag] ?? 'N/A';
                                    }else{
                                        return [];
                                    }
                                });
                        }
                    ) ->searchable()
                    ->optionsLimit(5)
                    ->label(__('Item Name'))
                    ->native(false),
                Forms\Components\TextInput::make('number')
                    ->maxLength(191),
                Forms\Components\DatePicker::make('date')->native(false),
                Forms\Components\DatePicker::make('due_date')->native(false),
                Toggle::make('status')
                    ->inline(false)
                    ->label(__('Status')),
                Forms\Components\FileUpload::make('file')
                    ->label(__('File'))
                    ->directory('Calibration'),
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
                            ->schema(components: [

                                TextEntry::make('item.name')
                                    ->label('Name'),
                                TextEntry::make('item.test_tag')
                                    ->label('Test Tag'),
                                TextEntry::make('number')
                                    ->label('Calibration Number')
                                   ,
                                TextEntry::make('date')
                                    ->label('Date'),
                                TextEntry::make('due_date')
                                    ->label('Due Date')
                                    ->color('primary'),
                                TextEntry::make('cost')
                                    ->prefix('€')
                                    ->suffixAction(
                                        Action::make('copyCostToPrice')
                                            ->icon('heroicon-m-clipboard')
                                            ->requiresConfirmation()
                                            ->action(function (Model $record) {
                                                $record->file;
                                            })
                                    ),
                                IconEntry::make('status')
                                    ->boolean()
                                    ->label('Status'),
                            ])->columns(7),
                        Tabs\Tab::make('Restocks')
                            ->label('Calibration Log')
                            ->icon('heroicon-o-folder')
                            ->iconPosition(IconPosition::After)
                            ->schema([
                                RepeatableEntry::make('calibrationOut')
                                    ->label('Calibration Log')
                                    ->schema([
                                        TextEntry::make('number')
                                            ->label('Number'),
                                        TextEntry::make('date')
                                            ->label('Date'),
                                        TextEntry::make('due_date')
                                            ->label('Due Date'),
                                    ])->columns(3)
                                    ->columnSpanFull()
                                    ->contained(true)
                            ])->columns(4),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()->recordUrl(
            fn (Model $record): string => Pages\ViewCalibration::getUrl([$record->id]),
        )
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable() ->copyable()
                    ->copyMessage('Test Tag copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('file')
                    ->label('File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->file ? asset('storage/' . $record->file) : null, true)
                    ->openUrlInNewTab()
                    ->tooltip(fn($state) => $state ? 'Download Calibration File' : 'No File Available')
                    ->color(fn($record) => $record->file ? 'primary' : 'secondary'),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-scale',
                        '1' => 'heroicon-o-scale',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        '0' => 'Expired Calibration',
                        '1' => 'Valid Calibration',
                        default => 'Unknown State',
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
            'index' => Pages\ListCalibrations::route('/'),
            'create' => Pages\CreateCalibration::route('/create'),
            'edit' => Pages\EditCalibration::route('/{record}/edit'),
            'view' => Pages\ViewCalibration::route('/{record}'),
        ];
    }
}

//
//
//Action::make('download')
//    ->label('Download File')
//    ->action(action: function ($record) {
//        if ($record->file) {
//            return redirect()->to(Storage::url($record->file));
//        }
//        Notification::make()
//            ->title('File Not Found')
//            ->danger()
//            ->body('The requested file is not available for download.')
//            ->send();
//    }),
