@php
    $crownBlockExists = $getRecord()->crownBlock()->exists();
@endphp

<div class="flex gap-2">
    <x-filament::button
        :color="$crownBlockExists ? 'success' : 'gray'"
        size="sm"
        tag="a"
        :href="$crownBlockExists
            ? route('filament.app.resources.crown-block.mains.index', ['wo_id' => $getRecord()->wo_id])
            : route('filament.app.resources.crown-block.mains.create', ['wo_id' => $getRecord()->wo_id])"
        target="_blank"
    >
        CB
    </x-filament::button>

    <x-filament::button
        :color="$crownBlockExists ? 'success' : 'gray'"
        size="sm"
        tag="a"
        :href="$crownBlockExists
            ? route('filament.app.resources.crown-block.mains.index', ['wo_id' => $getRecord()->wo_id])
            : route('filament.app.resources.crown-block.mains.create', ['wo_id' => $getRecord()->wo_id])"
        target="_blank"
    >
        TDS
    </x-filament::button>

    <x-filament::button
        :color="$crownBlockExists ? 'success' : 'gray'"
        size="sm"
        tag="a"
        :href="$crownBlockExists
            ? route('filament.app.resources.crown-block.mains.index', ['wo_id' => $getRecord()->wo_id])
            : route('filament.app.resources.crown-block.mains.create', ['wo_id' => $getRecord()->wo_id])"
        target="_blank"
    >
        BOWL
    </x-filament::button>
</div>
