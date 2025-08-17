<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Stock;

class ItemsBar extends ApexChartWidget
{
    use HasWidgetShield;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'itemsBar';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Items Count by Warehouse';

    /**
     * Widget column span to take full width
     *
     * @var array|string|int
     */
    protected array|string|int $columnSpan = 'full';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $stockCounts = Stock::with('warehouse')
        ->get()
        ->groupBy(fn($stock) => $stock->warehouse->name ?? 'Unknown')
        ->map(fn($stocks) => $stocks->count())
        ->toArray();

        $warehouses = array_keys($stockCounts);
        $counts = array_values($stockCounts);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Stock Count',
                    'data' => $counts,
                ],
            ],
            'xaxis' => [
                'categories' => $warehouses,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#f43f5e', '#ef4444', '#6366f1'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
