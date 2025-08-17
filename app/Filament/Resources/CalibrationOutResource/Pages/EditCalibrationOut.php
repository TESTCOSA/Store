<?php

namespace App\Filament\Resources\CalibrationOutResource\Pages;

use App\Filament\Resources\CalibrationOutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalibrationOut extends EditRecord
{
    protected static string $resource = CalibrationOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
