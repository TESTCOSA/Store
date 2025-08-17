<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAttendanceResource\Pages;
use App\Filament\Resources\UserAttendanceResource\RelationManagers;
use App\Filament\Widgets\AttendanceStatisticsChart;
use App\Models\UserAttendance;
use App\Models\UserDetails;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserAttendanceResource extends Resource
{
    protected static ?string $model = UserDetails::class;

    protected static ?string $navigationLabel = 'User Attendances';
    protected static ?string $modelLabel = 'User Attendances';
    protected static ?string $navigationGroup = 'HR Attendance';
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function getEloquentQuery(): Builder
    {            return parent::getEloquentQuery()->whereHas('user', function ($query) {
                    $query->where('enabled', '=', '1')->where('user_id', '!=', '1');
                });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('full_name_en')
                    ->label('English Name'),
                TextEntry::make('full_name_ar')
                    ->label('Arabic Name'),


            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(  'full_name_ar')
                    ->searchable()
                    ->label('الأسم الكامل'),
                Tables\Columns\TextColumn::make(  'full_name_en')
                    ->searchable()
                    ->label('Full Name'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
           UserAttendanceResource\RelationManagers\AttendanceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserAttendances::route('/'),
            'create' => Pages\CreateUserAttendance::route('/create'),
            'edit' => Pages\EditUserAttendance::route('/{record}/edit'),
            'view' => Pages\ViewUsersAttendance::route('/{record}'),
        ];
    }

//    public static function getWidgets(): array
//    {
//        return [
//            AttendanceStatisticsChart::class,
//        ];
//    }
}
