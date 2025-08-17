<?php

namespace App\Filament\Resources\TravelingBlockResource\Pages;

use App\Filament\Resources\TravelingBlockResource;
use App\Models\TravelingBlock\Main as TravelingBlock;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class LogsPage extends Page
{
    protected static string $resource = TravelingBlockResource::class;

    public TravelingBlock $record;
    public $logs;

    public function mount(TravelingBlock $record): void
    {
        $this->record = $record;
        $this->logs = DB::table('ws_event_log')
            ->where('table_name', 'ws_certifications_traveling_block')
            ->where('item_id', $record->certification_id)
            ->orderByDesc('log_id')
            ->get();
    }

    protected static string $view = 'filament.resources.traveling-block-resource.pages.logs-page';
}

