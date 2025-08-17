<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceSheet;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AttendanceChart extends ApexChartWidget
{
    protected static ?string $chartId = 'attendance-chart';
    protected static ?string $heading = 'Attendance Overview';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $sheets = AttendanceSheet::orderBy('period_start', 'desc')
            ->take(120)
            ->get()
            ->reverse();

        $labels = $sheets->pluck('period_label')->toArray();
        $presentSeries = $sheets->pluck('present_count')->toArray();
        $absentSeries  = $sheets->pluck('absent_count')->toArray();

        return [
            'series' => [
                [
                    'name' => 'Present',
                    'data' => $presentSeries,
                ],
                [
                    'name' => 'Absent',
                    'data' => $absentSeries,
                ],
            ],
            'categories' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'chart' => [
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'columnWidth' => '45%',
                ],
            ],
            'xaxis' => [
                'labels' => [
                    'rotate' => -45,
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Count',
                ],
            ],
        ];
    }
}
