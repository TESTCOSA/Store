<?php

namespace App\Filament\Resources\DamagedResource\Pages;

use App\Filament\Resources\DamagedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDamageds extends ListRecords
{
    protected static string $resource = DamagedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
