<?php

namespace App\Filament\Resources\CrownBlock\MainResource\Pages;

use App\Filament\Resources\CrownBlock\MainResource as CrownBlockResource;
use App\Models\ChecklistDetail;
use App\Models\CrownBlock;
use App\Models\CrownBlock\Checklist;
use App\Models\CrownBlock\ChecklistDetail as Details ;
use App\Models\WorkOrder;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
//use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EditMain extends EditRecord
{
    protected static string $resource = CrownBlockResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $checklistId = $data['checklist_id'] ?? 212;
        $certId = $data['certification_id'] ?? null;

        $items = ChecklistDetail::query()
            ->where('checklist_id', $checklistId)
            ->orderBy('sortorder')
            ->get();

        $savedResults = Details::query()
            ->where('cert_id', $certId)
            ->pluck('pass_fail', 'checklist_item_id')
            ->toArray();

        $data['checklist'] = [
            [
                'results' => collect($items)->mapWithKeys(fn ($item) => [
                    $item->item_id => $savedResults[$item->item_id] ?? '1',
                ])->toArray(),
                'checklist_id' => $checklistId,
            ]
        ];
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->form->getState();

        Details::where('cert_id', $record->certification_id)->delete();

        $results = $data['checklist'][0]['results'] ?? [];
        foreach ($results as $itemId => $result) {
            Details::create([
                'cert_id' => $record->certification_id,
                'checklist_id' => $record->checklist_id,
                'checklist_item_id' => $itemId,
                'pass_fail' => $result,
            ]);
        }
    }
}
