<?php

namespace App\Filament\Resources\StockResource\Pages;

use App\Filament\Resources\StockResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {

        return [

            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->slideOver()
                ->color("info")
                ->afterImport(function ($data, $livewire, $excelImportAction) {
                    // Count the number of items imported
                    $importedCount = $data->count();

                    // Display a success notification
                    Notification::make()
                        ->title('Import Successful')
                        ->success()
                        ->body("Successfully imported $importedCount items.")
                        ->send();
                }),
        ];
    }
    public function getTabs(): array
    {

        $warehouses = \App\Models\Warehouse::all();

        $tabs = [
            null => Tab::make(__('All'))
                ->label(__('All')),
        ];
        foreach ($warehouses as $warehouse) {
            $tabs[$warehouse->id] = Tab::make($warehouse->name)
                ->query(fn ($query) => $query->where('warehouse_id', $warehouse->id))
                ->label($warehouse->name);
        }

        return $tabs;
    }

}
