<?php

namespace App\Filament\Resources\StockOutResource\Pages;

use App\Notifications\RequestApprovalNotification;
use App\Filament\Resources\StockOutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
class CreateStockOut extends CreateRecord
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {


        $data['request_by'] = auth()->id();
        $data['request_date'] = now();
        $data['approved_by'] = null;
        $data['approved'] = false;
        $data['approve_date'] = null;
        $data['status'] = 0;

        return $data;
    }
    
    
  protected function afterCreate(): void
{
    $stockOut = $this->record; // Get the newly created record

    // Fetch the user who made the request
    $requestedBy = $stockOut->outUserRequested->full_name_en ?? 'Unknown User';

    // Fetch the items in the request
    $items = [];
    foreach ($stockOut->outDetails as $detail) {
        $items[] = [
            'name' => $detail->outItems->name ?? 'Unknown Item',
            'quantity' => $detail->quantity,
        ];
    }

    // Prepare the request data
    $requestData = [
        'request_number' => $stockOut->id,
        'requested_by' => $requestedBy,
        'wo_id' => $stockOut->wo_id,
        'items' => $items,
    ];

    // Fetch users with the 'NewRequest' role
    $users = User::role('NewRequest')->get();

    // Send notifications to each user
    foreach ($users as $user) {
        $user->notify(new RequestApprovalNotification($requestData, 'new_request'));
    }
}

    protected static string $resource = StockOutResource::class;



}
