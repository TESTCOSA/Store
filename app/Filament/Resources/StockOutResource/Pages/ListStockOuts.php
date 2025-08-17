<?php

namespace App\Filament\Resources\StockOutResource\Pages;

use App\Filament\Resources\StockOutResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListStockOuts extends ListRecords
{
    protected static string $resource = StockOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
