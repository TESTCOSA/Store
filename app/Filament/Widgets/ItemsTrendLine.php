<?php
namespace App\Filament\Widgets;

use Filament\Forms\Components\DatePicker;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\StockInDetails;
use App\Models\StockOutDetails;
use Carbon\Carbon;
use DatePeriod;
use DateInterval;

class ItemsTrendLine extends ApexChartWidget
{
    protected static ?string $chartId = 'itemsTrendLine';
    protected static ?string $heading = 'Stock-In vs. Stock-Out (Last 30 Days)';
    protected static bool $hasFilters = true; // Enable filters
    protected static ?string $maxWidth = 'full';
    protected array|string|int $columnSpan = 2;


    /**
     * Chart Options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Use filters from the form, or default to last 30 days
        $startDate = $this->filters['date_start'] ?? Carbon::now()->subDays(29)->startOfDay();
        $endDate = $this->filters['date_end'] ?? Carbon::now()->endOfDay();

        // Retrieve stock-in data
        $stockIn = StockInDetails::whereHas('stockIn', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('stocked_date', [$startDate, $endDate]);
        })
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Retrieve stock-out data
        $stockOut = StockOutDetails::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare arrays for the chart
        $dates = [];
        $stockInCounts = [];
        $stockOutCounts = [];

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dates[] = $date->format('M d');

            $stockInRecord = $stockIn->firstWhere('date', $dateString);
            $stockInCounts[] = $stockInRecord ? (int) $stockInRecord->count : 0;

            $stockOutRecord = $stockOut->firstWhere('date', $dateString);
            $stockOutCounts[] = $stockOutRecord ? (int) $stockOutRecord->count : 0;
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
            ],
            'series' => [
                [
                    'name' => 'Stock-In (Received)',
                    'data' => $stockInCounts,
                ],
                [
                    'name' => 'Stock-Out (Requested)',
                    'data' => $stockOutCounts,
                ],
            ],
            'xaxis' => [
                'categories' => $dates,
            ],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'markers' => [
                'size' => 4,
            ],
            'tooltip' => [
                'x' => [
                    'format' => 'MMM dd',
                ],
            ],
            'colors' => ['#10b981', '#ef4444'], // Green for stock-in, Red for stock-out
        ];
    }

    /**
     * Filters for the chart
     */
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('date_start')
                ->label('Start Date')
                ->default(Carbon::now()->subDays(29)->startOfDay()),

            DatePicker::make('date_end')
                ->label('End Date')
                ->default(Carbon::now()->endOfDay()),
        ];
    }
}
