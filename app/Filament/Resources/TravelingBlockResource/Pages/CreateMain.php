<?php

namespace App\Filament\Resources\TravelingBlockResource\Pages;

use App\Filament\Resources\TravelingBlockResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMain extends CreateRecord
{
    protected static string $resource = TravelingBlockResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['inspector_id'] = Auth::id();
        $data['date_added'] = now();
        return $data;
    }
}

