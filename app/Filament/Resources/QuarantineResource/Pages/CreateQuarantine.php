<?php

namespace App\Filament\Resources\QuarantineResource\Pages;

use App\Filament\Resources\QuarantineResource;
use App\Models\Item;
use App\Models\Stock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuarantine extends CreateRecord
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {


        $item = Stock::find($data['stock_id'])->item;
        $item->update([
            'status' => 6,
        ]);
        $data['item_id'] = $item->id;
        $data['user_id'] = auth()->id();
        $data['status'] = 0;
        $data['quarantined_at'] = now();
        $data['released_at'] = null;

        return $data;
    }
    protected static string $resource = QuarantineResource::class;
}
