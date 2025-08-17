<?php

namespace App\Filament\Resources\CalibrationOutResource\Pages;

use App\Filament\Resources\CalibrationOutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCalibrationOut extends CreateRecord
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['calibration_by'] = auth()->id();
        $data['calibration_stock_out_date'] = now();
        $data['approved_by'] = null;
        $data['approve_date'] = null;
        $data['approved'] = 0;
        $data['status'] = 0;

        return $data;
    }
    protected static string $resource = CalibrationOutResource::class;
}
