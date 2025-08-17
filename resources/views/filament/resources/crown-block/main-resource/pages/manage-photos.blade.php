<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div class="mt-6">
            <x-filament::button type="submit">
                Save New Photos
            </x-filament::button>
        </div>
    </form>
    <div class="mt-12">
        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
            Uploaded Photos
        </h3>
        {{ $this->photosInfolist }}
    </div>
</x-filament-panels::page>
