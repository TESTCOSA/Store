<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Database\Eloquent\Builder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Item;
use App\Models\Warehouse;

class ItemsPie extends ApexChartWidget
{

    use HasWidgetShield;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'itemsPie';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Items in Stock by Warehouse';


    /**
     * Fetch data filtered by warehouse
     *
     * @param string|null $warehouseId
     * @return array
     */
    protected function getFilteredData(?string $warehouseId): array
    {
        $query = Item::query();

        // Filter items by warehouse if warehouse ID is provided
        if (!empty($warehouseId)) {
            $query->whereHas('stock', function (Builder $stockQuery) use ($warehouseId) {
                $stockQuery->where('warehouse_id', $warehouseId);
            });
        }


        // Aggregate status counts
        $data = $query->selectRaw('
            COUNT(CASE WHEN status = 1 THEN 1 END) as available,
            COUNT(CASE WHEN status = 2 THEN 1 END) as maintenance,
            COUNT(CASE WHEN status = 3 THEN 1 END) as calibration,
            COUNT(CASE WHEN status = 4 THEN 1 END) as in_use,
            COUNT(CASE WHEN status = 5 THEN 1 END) as missing,
            COUNT(CASE WHEN status = 6 THEN 1 END) as quarantined,
            COUNT(CASE WHEN status = 7 THEN 1 END) as damaged
        ')->first();

        return [
            $data->available ?? 0,
            $data->maintenance ?? 0,
            $data->calibration ?? 0,
            $data->in_use ?? 0,
            $data->missing ?? 0,
            $data->quarantined ?? 0,
            $data->damaged ?? 0,
        ];
    }

    /**
     * Chart options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Aggregate data across warehouses; example assumes you have a relation for warehouse
        $data = Warehouse::withCount([
            'items as available' => function($query) {
                $query->whereHas('stock', function ($stockQuery) {
                    $stockQuery->where('warehouse_id', '>', 0);
                })->where('status', 1);
            },
            'items as maintenance' => function($query) {
                $query->where('status', 2);
            },
            'items as calibration' => function($query) {
                $query->where('status', 3);
            },
            'items as in_use' => function($query) {
                $query->where('status', 4);
            },
            'items as missing' => function($query) {
                $query->where('status', 5);
            },
            'items as quarantined' => function($query) {
                $query->where('status', 6);
            },
            'items as damaged' => function($query) {
                $query->where('status', 7);
            },
        ])->get();

        // Prepare series data for each status across all warehouses
        $warehouses = $data->pluck('name');
        $available = $data->pluck('available');
        $maintenance = $data->pluck('maintenance');
        $calibration = $data->pluck('calibration');
        $inUse = $data->pluck('in_use');
        $missing = $data->pluck('missing');
        $quarantined = $data->pluck('quarantined');
        $damaged = $data->pluck('damaged');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'stacked' => true,
            ],
            'series' => [
                ['name' => 'Available', 'data' => $available],
                ['name' => 'Need Maintenance', 'data' => $maintenance],
                ['name' => 'Need Calibration', 'data' => $calibration],
                ['name' => 'In Use', 'data' => $inUse],
                ['name' => 'Missing', 'data' => $missing],
                ['name' => 'Quarantined', 'data' => $quarantined],
                ['name' => 'Damaged', 'data' => $damaged],
            ],
            'xaxis' => [
                'categories' => $warehouses,
            ],
            'colors' => [
                '#34d399', // Success - Green
                '#6b7280', // Secondary - Gray
                '#3b82f6', // Info - Blue
                '#f59e0b', // Warning - Yellow
                '#ef4444', // Danger - Red
                '#374151', // Dark - Dark Gray
                '#b91c1c', // Alternative red
            ],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }


    protected static ?string $pollingInterval = '15s';
}
