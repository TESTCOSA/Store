<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Item;

class OverallItemsDonut extends ApexChartWidget
{
    protected static ?string $chartId = 'overallItemsDonut';
    protected static ?string $heading = 'Overall Items Distribution';
    protected static bool $hasFilters = false;

    /**
     * Chart Options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Aggregate overall data from items
        $data = Item::selectRaw('
            COUNT(CASE WHEN status = 1 THEN 1 END) as available,
            COUNT(CASE WHEN status = 2 THEN 1 END) as maintenance,
            COUNT(CASE WHEN status = 3 THEN 1 END) as calibration,
            COUNT(CASE WHEN status = 4 THEN 1 END) as in_use,
            COUNT(CASE WHEN status = 5 THEN 1 END) as missing,
            COUNT(CASE WHEN status = 6 THEN 1 END) as quarantined,
            COUNT(CASE WHEN status = 7 THEN 1 END) as damaged
        ')->first();

        $series = [
            $data->available ?? 0,
            $data->maintenance ?? 0,
            $data->calibration ?? 0,
            $data->in_use ?? 0,
            $data->missing ?? 0,
            $data->quarantined ?? 0,
            $data->damaged ?? 0,
        ];

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $series,
            'labels' => [
                'Available',
                'Need Maintenance',
                'Need Calibration',
                'In Use',
                'Missing',
                'Quarantined',
                'Damaged'
            ],
            'colors' => [
                '#34d399', // Available (Green)
                '#6b7280', // Maintenance (Gray)
                '#3b82f6', // Calibration (Blue)
                '#f59e0b', // In Use (Yellow)
                '#ef4444', // Missing (Red)
                '#374151', // Quarantined (Dark Gray)
                '#b91c1c', // Damaged (Alternative Red)
            ],
            'legend' => [
                'position' => 'bottom',
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
