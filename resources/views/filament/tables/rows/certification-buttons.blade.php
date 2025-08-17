<div class="flex gap-2">
    <x-filament::button
        color="primary"
        tag="a"
        href="{{ route('filament.app.resources.crown-block.mains.index', ['wo_id' => $record->wo_id]) }}"
        target="_blank"
    >
        CB
    </x-filament::button>

    <x-filament::button
        color="success"
        tag="a"
        href="{{ route('filament.app.resources.tds.index', ['wo_id' => $record->wo_id]) }}"
        target="_blank"
    >
        TDS
    </x-filament::button>

    <x-filament::button
        color="warning"
        tag="a"
        href="{{ route('filament.app.resources.bowl.index', ['wo_id' => $record->wo_id]) }}"
        target="_blank"
    >
        BOWL
    </x-filament::button>
</div>
