<?php

namespace App\Filament\Resources\MaintenanceResource\Pages;

use App\Filament\Resources\MaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenance extends CreateRecord
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['maintenance_by'] = auth()->id();
        $data['maintenance_stock_out_date'] = now();
        $data['approved_by'] = null;
        $data['approve_date'] = null;
        $data['approved'] = 0;
        $data['status'] = 0;

        return $data;
    }
    protected static string $resource = MaintenanceResource::class;
}
