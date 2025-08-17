<?php

namespace App\Filament\Resources\MissingItemsResource\Pages;

use App\Filament\Resources\MissingItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMissingItems extends EditRecord
{
    protected static string $resource = MissingItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
