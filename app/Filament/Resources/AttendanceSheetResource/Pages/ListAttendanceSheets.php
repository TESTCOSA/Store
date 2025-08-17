<?php

namespace App\Filament\Resources\AttendanceSheetResource\Pages;

use App\Filament\Resources\AttendanceSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceSheets extends ListRecords
{
    protected static string $resource = AttendanceSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
            Actions\Action::make('monthly_sheet')
                ->label('Create Monthly Sheet')
                ->url(route('filament.app.resources.attendance-sheets.monthly-sheet'))
                ->color('success')
                ->icon('heroicon-o-calendar'),
        ];
    }
}
