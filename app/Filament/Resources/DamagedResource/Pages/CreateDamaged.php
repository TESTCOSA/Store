<?php

namespace App\Filament\Resources\DamagedResource\Pages;

use App\Filament\Resources\DamagedResource;
use App\Models\Stock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDamaged extends CreateRecord
{



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Fetch the related item from the stock
        $item = Stock::find($data['stock_id'])->item;

        // Update the item's status
        $item->update([
            'status' => 7, // Assuming 6 represents the desired status
        ]);

        $data['item_id'] = $item->id;
        $data['user_id'] = auth()->id();
        $data['status'] = 0; // Default status
        $data['quarantined_at'] = now();
        $data['released_at'] = null; // Default null for released_at
        $data['stock_out_id'] = $data['stock_out_id'] ?? null; // Optional field
        $data['damaged_by'] = $data['damaged_by'] ?? null; // Optional field
        $data['work_order_id'] = $data['work_order_id'] ?? null; // Optional field
        $data['reported_by'] = $data['reported_by'] ?? auth()->id(); // Default to current user if not provided
        $data['warehouse_id'] = $data['warehouse_id'] ?? null; // Optional field
        $data['quantity'] = $data['quantity'] ?? 1; // Default to 1 if not provided
        $data['resolved_by'] = $data['resolved_by'] ?? null; // Optional field
        $data['reported_at'] = $data['reported_at'] ?? now(); // Default to now if not provided
        $data['resolved_at'] = $data['resolved_at'] ?? null; // Optional field
        $data['description'] = $data['description'] ?? null; // Optional field

        return $data;
    }


    protected static string $resource = DamagedResource::class;
}
