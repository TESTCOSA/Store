<?php

namespace App\Filament\Resources\CrownBlock\MainResource\Pages;
use App\Filament\Resources\CrownBlock\MainResource as CrownBlockResource;
use App\Models\ChecklistDetail;
use App\Models\CrownBlock;
use App\Models\CrownBlock\Checklist;
use App\Models\CrownBlock\ChecklistDetail as Details;
use App\Models\WorkOrder;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CreateMain extends CreateRecord
{
    protected static string $resource = CrownBlockResource::class;

    public ?int $wo_id = null;
    public ?int $checklist_id = null;

    public function mount(): void
    {
        parent::mount();

        $this->wo_id = request()->route('wo_id');

        if ($this->wo_id) {
            $this->form->fill([
                'wo_id' => $this->wo_id,
                'checklist' => [
                    [
                        'checklist_id' => 212,
                        'results' => ChecklistDetail::where('checklist_id', 212)
                            ->orderBy('sortorder')
                            ->get()
                            ->mapWithKeys(fn ($item) => [$item->item_id => '1'])
                            ->toArray(),
                    ],
                ],
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->checklist_id = $data['checklist'][0]['checklist_id'];

        $data['wo_id'] = $this->wo_id;
        $data['standard_type_api'] = $data['standard_type_api'] ? '1' : '2';
        $data['standard_type_astm'] = $data['standard_type_astm'] ? '1' : '2';
        $data['customer_id'] = 0;
        $data['approved_by'] = Auth::id() ?? 1;
        $data['date_added'] = now();
        $data['sequence'] = 0;
        $data['checklist_id'] = $this->checklist_id;
        $data['inspector_id'] = Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $data = $this->form->getState();

        $wo = WorkOrder::findOrFail($record->wo_id);
        $record->update([
            'customer_id' => $wo->customer_id,
            'sequence' => $wo->sequence,
        ]);

        Checklist::create([
            'cert_id' => $record->certification_id,
            'wo_id' => $record->wo_id,
            'customer_id' => $wo->customer_id,
            'checklist_id' => $this->checklist_id,
            'inspector_id' => $record->inspector_id,
            'checklist_date' => $record->test_date,
            'user_id' => Auth::id(),
            'date_added' => now(),
            'eq_type' => 42,
        ]);

        $results = $data['checklist'][0]['results'] ?? [];
        foreach ($results as $itemId => $result) {
            Details::create([
                'cert_id' => $record->certification_id,
                'checklist_id' => $this->checklist_id,
                'checklist_item_id' => $itemId,
                'pass_fail' => $result,
            ]);
        }

        DB::table('ws_event_log')->insert([
            'added_ip' => request()->ip(),
            'added_date' => now(),
            'added_by' => Auth::id(),
            'table_name' => 'certifications_crown_block',
            'item_id' => $record->certification_id,
            'action_name' => 'Crown Block Certification #' . $record->certification_id . ' Created.',
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return CrownBlockResource::getUrl('index', ['wo_id' => $this->wo_id]);
    }
}
