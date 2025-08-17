<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Exports\ItemExporter;
use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->slideOver()
                ->color("info"),
            Actions\CreateAction::make()


        ];
    }
}
