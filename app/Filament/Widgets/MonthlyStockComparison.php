<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\StockInDetails;
use App\Models\StockOutDetails;

class MonthlyStockComparison extends ApexChartWidget
{
    protected static ?string $chartId = 'monthlyStockComparison';
    protected static ?string $heading = 'Monthly Stock In vs. Stock Out (Current Year)';
    protected static bool $hasFilters = false;
    protected static ?string $maxWidth = 'full';

    protected function getOptions(): array
    {
        // Define current year boundaries.
        $currentYear = Carbon::now()->year;
        $months = [];
        $stockInData = [];
        $stockOutData = [];

        // Loop through each month (January to December)
        for ($month = 1; $month <= 12; $month++) {
            $startMonth = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $endMonth = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();
            $months[] = $startMonth->format('M');

            // Sum of stock-in quantities for the month
            $stockInTotal = StockInDetails::whereHas('stockIn', function ($query) use ($startMonth, $endMonth) {
                $query->whereBetween('stocked_date', [$startMonth, $endMonth]);
            })
                ->selectRaw('SUM(quantity) as total')
                ->value('total') ?? 0;

            // Sum of stock-out quantities for the month
            $stockOutTotal = StockOutDetails::whereBetween('created_at', [$startMonth, $endMonth])
                ->selectRaw('SUM(quantity) as total')
                ->value('total') ?? 0;

            $stockInData[] = (int)$stockInTotal;
            $stockOutData[] = (int)$stockOutTotal;
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%', // Full width chart
                'stacked' => false,
            ],
            'series' => [
                [
                    'name' => 'Stock In',
                    'data' => $stockInData,
                ],
                [
                    'name' => 'Stock Out',
                    'data' => $stockOutData,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
            ],
            'colors' => ['#10b981', '#ef4444'], // Green for stock in, Red for stock out
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'dataLabels' => [
                        'position' => 'top',
                    ],
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
