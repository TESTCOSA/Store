<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\UserDetails;

class UserRequestsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'userRequestsChart';
    protected static ?string $heading = 'User Requests vs. Active Items';
    protected static bool $hasFilters = false;
    protected static ?string $maxWidth = 'full';
    protected array|string|int $columnSpan = 2;



    protected static bool $deferLoading = true;
    protected static ?string $loadingIndicator = 'Loading...';

    protected function getOptions(): array
    {
$users = UserDetails::whereHas('userGroups', function ($query) {
    $query->where('group_id', 4);
})
    ->whereHas('user', function ($query) {
        $query->where('enabled', '=', '1');
    })
    ->whereHas('stockOutItems')
    ->with('user')
    ->withCount('stockOutItems')
    ->withCount([
        'stockOutItems as active_items' => function ($query) {
            $query->where(function ($q) {
                $q->where('inv_stock_out.status', 0)  // Status is 0 or 2 for active items
                    ->orWhere('inv_stock_out.status', 2);
            })
            ->where('returned', false)
            ->where('inv_stock_out.approved', 1) // Only approved items
            ->whereHas('outItems', function ($query) { // Filter based on the item's type
                $query->whereHas('category', function ($q) {
                    $q->whereHas('type', function ($qq) {
                        // Check if item is returnable and not consumable
                        $qq->where('is_consumable', false)
                            ->where('is_returned', true);
                    });
                });
            });
        }
    ])
    ->get();

$labels = $users->pluck('full_name_en')->toArray();
$totalRequested = $users->pluck('stock_out_items_count')->toArray();
$activeItems = $users->pluck('active_items')->toArray();




        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%', // Ensure full width
                'stacked' => true,
            ],
            'series' => [
                [
                    'name' => 'Total Requested Items',
                    'data' => $totalRequested,
                ],
                [
                    'name' => 'Active Items',
                    'data' => $activeItems,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
            ],
            'colors' => ['#3b82f6', '#34d399'],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'dataLabels' => [
                        'position' => 'top', // Display data labels on top of bars
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'colors' => ['#000000'], // Set text color for better visibility
                ],
            ],
            'tooltip' => [
                'enabled' => true,
                'y' => [
                    'formatter' => "function(val) { return val + ' Requests'; }",
                ],
            ],
            'legend' => [
                'position' => 'bottom',
            ],
            'responsive' => [
                [
                    'breakpoint' => 768,
                    'options' => [
                        'chart' => [
                            'height' => 400, // Adjust height for smaller screens
                        ],
                    ],
                ],
            ],
        ];

    }
}
