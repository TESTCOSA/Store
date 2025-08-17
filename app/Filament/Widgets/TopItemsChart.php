<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\StockInDetails;
use App\Models\StockOutDetails;

class TopItemsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'topItemsChart';
    // Updated heading to reflect both Stock In and Stock Out data
    protected static ?string $heading = 'Top 5 Items: Stock In vs. Stock Out';
    protected static bool $hasFilters = false;
    protected static ?string $maxWidth = 'full';

    protected function getOptions(): array
    {
        // Retrieve the top 5 items by received stock (Stock In)
        $topItems = StockInDetails::with('inItems')
            ->selectRaw('item_id, SUM(quantity) as total_received')
            ->groupBy('item_id')
            ->orderByDesc('total_received')
            ->take(5)
            ->get();

        $labels = [];
        $stockInData = [];
        $stockOutData = [];

        foreach ($topItems as $record) {
            // Use the item name if available; otherwise, fall back to 'Unknown'
            $itemName = $record->inItems ? $record->inItems->name : 'Unknown';
            $labels[] = $itemName;
            $stockInData[] = (int)$record->total_received;

            // For each top item, calculate the total stock out quantity
            $stockOutTotal = StockOutDetails::where('item_id', $record->item_id)->sum('quantity');
            $stockOutData[] = (int)$stockOutTotal;
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%', // Full width
            ],
            'series' => [
                [
                    'name' => 'Stock In (Received)',
                    'data' => $stockInData,
                ],
                [
                    'name' => 'Stock Out (Requested)',
                    'data' => $stockOutData,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
            ],
            'colors' => ['#10b981', '#ef4444'], // Green for Stock In, Red for Stock Out
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true, // Display bars horizontally
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'tooltip' => [
                'enabled' => true,
                'y' => [
                    'formatter' => "function(val) { return val + ' units'; }",
                ],
            ],
            'legend' => [
                'position' => 'bottom',
            ],
        ];
    }
}
