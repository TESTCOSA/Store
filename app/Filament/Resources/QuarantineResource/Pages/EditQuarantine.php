<?php

namespace App\Filament\Resources\QuarantineResource\Pages;

use App\Filament\Resources\QuarantineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuarantine extends EditRecord
{
    protected static string $resource = QuarantineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
