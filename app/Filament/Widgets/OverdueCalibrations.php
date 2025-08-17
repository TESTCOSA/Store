<?php
namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Item;
use Carbon\Carbon;

class OverdueCalibrations extends ApexChartWidget
{
    protected static ?string $chartId    = 'overdueCalibrations';
    protected static ?string $heading    = 'Overdue Calibrations';
    protected static bool   $hasFilters  = false;
    protected static ?string $maxWidth   = 'xs';
    protected array|string|int $columnSpan = 1;

   protected function getOptions(): array
{
    $total = Item::has('calibrations')->count();
    $overdue = Item::whereHas('calibrations', function ($query) {
        $query->where('due_date', '>', Carbon::now());
    })->count();
    $percent = $total > 0 ? round($overdue / $total * 100, 1) : 0;

    $options = [
        'chart' => [
            'type'   => 'radialBar',
            'height' => 240,
        ],
        'series' => [$percent],
        'labels' => ['% Overdue'],
        'plotOptions' => [
            'radialBar' => [
                'dataLabels' => [
                    'name' => [
                        'fontSize' => '16px',
                    ],
                    'value' => [
                        'fontSize' => '28px',
                        'formatter' => "function (val) { return val + '%'; }",
                    ],
                    'total' => [
                        'show'  => true,
                        'label' => 'Count',
                        'formatter' => "function () { return {$overdue}; }",
                    ],
                ],
            ],
        ],
        'colors' => ['#EF4444'],
    ];

    // Uncomment the next line to debug the output
    // dd($options);
    
    return $options;
}

}

