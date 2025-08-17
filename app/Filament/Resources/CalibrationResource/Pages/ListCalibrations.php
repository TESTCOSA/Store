<?php

namespace App\Filament\Resources\CalibrationResource\Pages;

use App\Filament\Resources\CalibrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalibrations extends ListRecords
{
    protected static string $resource = CalibrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
