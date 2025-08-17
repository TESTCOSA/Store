<?php

namespace App\Filament\Resources\AttendanceApprovalResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'sheets';

    protected static ?string $recordTitleAttribute = 'approval_id';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('approval_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('userDetails.full_name_en')
                    ->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('present_count')
                    ->label('Present'),
                Tables\Columns\TextColumn::make('absent_count')
                    ->label('Absent'),
                Tables\Columns\TextColumn::make('annual_leave_count')
                    ->label('Annual Leave'),
                Tables\Columns\TextColumn::make('sick_leave_count')
                    ->label('Sick Leave'),
                Tables\Columns\TextColumn::make('public_holiday_count')
                    ->label('Public Holiday'),
                Tables\Columns\TextColumn::make('unpaid_leave_count')
                    ->label('Unpaid Leave'),
                    ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
