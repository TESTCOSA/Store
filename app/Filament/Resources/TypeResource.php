<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeResource\Pages;
use App\Filament\Resources\TypeResource\RelationManagers;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    protected static ?string $navigationLabel = 'Types';
    protected static ?string $modelLabel = 'Types';
    protected static ?string $navigationGroup = 'Assets';
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_calibrated')
                    ->required(),
                Forms\Components\Toggle::make('is_maintained')
                    ->required(),
                Forms\Components\Toggle::make('is_returned')
                    ->required(),
                Forms\Components\Toggle::make('is_consumable')
                    ->required(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                TextEntry::make('name'),
                IconEntry::make('is_calibrated')->boolean()
                    ->label('Calibration Required'),
                IconEntry::make('is_returned')->boolean()
                    ->label('Returnable'),
                IconEntry::make('is_consumable')->boolean()
                    ->label('Consumable'),
                RepeatableEntry::make('categories')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        IconEntry::make('enabled')->boolean()
                            ->label('Enabled'),
                    ])
                    ->columnSpanFull()
                    ->columns(2)
                    ->grid(3),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordUrl(
                fn (Model $record): string => Pages\ViewType::getUrl([$record->id]),
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_calibrated')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_maintained')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_returned')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_consumable')
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
            'view' => Pages\ViewType::route('/{record}'),
        ];
    }
}
