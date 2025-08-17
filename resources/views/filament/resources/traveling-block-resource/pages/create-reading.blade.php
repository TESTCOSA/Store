<x-filament-panels::page>
    <form wire:submit="create" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{ $this->form }}
        </div>
        <div class="flex justify-end">
            <x-filament::button type="submit">Save Reading</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
