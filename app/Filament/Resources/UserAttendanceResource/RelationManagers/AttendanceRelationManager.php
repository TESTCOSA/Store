<?php

namespace App\Filament\Resources\UserAttendanceResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class AttendanceRelationManager extends RelationManager
{
    protected static string $relationship = 'attendance';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('date')
                    ->required()
                    ->maxLength(255),
            ]);
    }


    public function getTabs(): array
    {
        $year = now()->year;

        // "All"
        $tabs = [
            'all' => Tab::make('All Periods'),
        ];

        // 12 payroll‐period tabs
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 21)->subMonth()->startOfDay();
            $end   = Carbon::create($year, $m, 20)->endOfDay();

            $tabs["{$year}-{$m}"] = Tab::make(Carbon::create($year, $m, 1)->format('M').' Payroll')
//                ->modifyQueryUsing(fn(Builder $q) => $q->whereBetween('date', [$start, $end]))
                ->query(fn ($query) => $query->whereBetween('date', [$start, $end]));
        }

        return $tabs;
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('Y-m-d')->searchable(),
                Tables\Columns\TextColumn::make('clock_in'),
                Tables\Columns\TextColumn::make('clock_out'),
            ])
            ->filters([
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

