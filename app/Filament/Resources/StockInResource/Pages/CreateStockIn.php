<?php

namespace App\Filament\Resources\StockInResource\Pages;

use App\Filament\Resources\StockInResource;
use App\Models\Stock;
use App\Models\StockIn;
use App\Models\User;
use App\Notifications\StockInNotification;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateStockIn extends CreateRecord
{


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['stocked_by'] = auth()->id();  // Set 'stocked_by' to the authenticated user
        $data['stocked_date'] = now();  // Use the current timestamp for stocked_date
        $data['approved_by'] = null;  // Set 'approved_by' to the authenticated user
        $data['approved'] = false;  // Set default approval status
        $data['approve_date'] = null;  // Default to null until approved
        $data['status'] = 0;  // Set the default status (or another value depending on your needs)

        return $data;
    }
    protected static string $resource = StockInResource::class;
}
