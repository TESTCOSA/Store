<?php

namespace App\Filament\Resources\InspectorsResource\Pages;

use App\Filament\Resources\InspectorsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInspectors extends ListRecords
{
    protected static string $resource = InspectorsResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
