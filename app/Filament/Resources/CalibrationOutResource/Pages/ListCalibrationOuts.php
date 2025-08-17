<?php

namespace App\Filament\Resources\CalibrationOutResource\Pages;

use App\Filament\Resources\CalibrationOutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalibrationOuts extends ListRecords
{
    protected static string $resource = CalibrationOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
