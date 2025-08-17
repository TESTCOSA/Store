<?php

namespace App\Filament\Resources\QuarantineResource\Pages;

use App\Filament\Resources\QuarantineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuarantines extends ListRecords
{
    protected static string $resource = QuarantineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
