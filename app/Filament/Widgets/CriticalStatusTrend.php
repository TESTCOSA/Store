<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Item;
use Carbon\Carbon;
use DatePeriod;
use DateInterval;

class CriticalStatusTrend extends ApexChartWidget
{
    protected static ?string $chartId    = 'criticalStatusTrend';
    protected static ?string $heading    = 'Critical Status Trend';
    protected static bool   $hasFilters  = true;
    protected static ?string $maxWidth   = 'full';
    protected array|string|int $columnSpan = 2;

    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        $months = ['all' => 'Last 12 Months'];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $key  = $date->format('Y-m');
            $label = $date->format('M Y');
            $months[$key] = $label;
        }
        $lastTwelve = array_slice($months, 1);
        $lastTwelve = array_reverse($lastTwelve, true);
        return ['all' => 'Last 12 Months'] + $lastTwelve;
    }

    protected function getOptions(): array
    {
        if ($this->filter === 'all') {
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate   = Carbon::now()->endOfMonth();
        } else {
            [$year, $month] = explode('-', $this->filter);
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate   = Carbon::create($year, $month, 1)->endOfMonth();
        }

        $data = Item::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                DATE(created_at) as date,
                SUM(CASE WHEN status = 5 THEN 1 ELSE 0 END) as missing,
                SUM(CASE WHEN status = 6 THEN 1 ELSE 0 END) as quarantined,
                SUM(CASE WHEN status = 7 THEN 1 ELSE 0 END) as damaged
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Build your $dates, $missingCounts, etc.
        $dates = [];
        $missingCounts = [];
        $quarantinedCounts = [];
        $damagedCounts = [];

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay());
        foreach ($period as $date) {
            $dates[] = $date->format('M d');
            $record = $data->firstWhere('date', $date->format('Y-m-d'));
            $missingCounts[]     = $record ? (int)$record->missing : 0;
            $quarantinedCounts[] = $record ? (int)$record->quarantined : 0;
            $damagedCounts[]     = $record ? (int)$record->damaged : 0;
        }

        return [
            'chart'   => ['type' => 'line', 'height' => 400, 'width' => '100%'],
            'series'  => [
                ['name' => 'Missing',     'data' => $missingCounts],
                ['name' => 'Quarantined', 'data' => $quarantinedCounts],
                ['name' => 'Damaged',     'data' => $damagedCounts],
            ],
            'xaxis'   => ['categories' => $dates],
            'stroke'  => ['curve' => 'smooth'],
            'markers' => ['size'  => 4],
            'tooltip' => ['x'     => ['format' => 'MMM dd']],
            'colors'  => ['#ef4444', '#374151', '#b91c1c'],
        ];
    }
}
