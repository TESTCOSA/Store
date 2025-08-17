<?php

namespace App\Filament\Widgets;

use App\Models\Stock;
use App\Models\Warehouse;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Item; // Import your Item model

class StatsOverview extends BaseWidget
{

    use HasWidgetShield;
    public static function canView(): bool
    {
        return Auth()->user()?->hasAnyRole('admin');
    }

    protected ?string $heading = 'Statistics';
    protected static ?int $sort = 1;
    protected ?string $description = 'An overview current count of the items company have in their inventory .';
    protected function getStats(): array
    {
        // Count available items (adjust the condition as per your table's structure)

        $stockItems = Stock::all();
        $tools = 0;
        $equipment = 0;
        $consumables = 0;
        foreach ($stockItems as $stock) {
            if($stock->item->category->type->is_calibrated and $stock->item->category->type->is_returned){
                $equipment += $stock->quantity;
            }else if(!$stock->item->category->type->is_calibrated and $stock->item->category->type->is_returned){
                $tools += $stock->quantity;
            }
            else if($stock->item->category->type->is_consumable){
                $consumables += $stock->quantity;
            }
        }
        $allItems = $tools + $equipment + $consumables;
        return [
            Stat::make('Tools', $tools)
                ->description('Total Tools ')
                ->color('success'),
            Stat::make('Equipment', $equipment)
                ->description('Total Equipments')
                ->color('success'),
            Stat::make('Consumables', $consumables)
                ->description('Total Consumables')
                ->color('success'),
            Stat::make('All', $allItems)
                ->description('All Items')
                ->color('success'),
        ];
    }
}
