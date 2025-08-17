<?php

namespace App\Filament\Resources\MissingItemsResource\Pages;

use App\Filament\Resources\MissingItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMissingItems extends ViewRecord
{
    protected static string $resource = MissingItemsResource::class;
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
