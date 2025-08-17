<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AttendanceChart;
use App\Filament\Widgets\AttendanceStatisticsChart;
use App\Filament\Widgets\Available;
use App\Filament\Widgets\CriticalStatusTrend;
use App\Filament\Widgets\EmployeeAttendanceAnalytics;
use App\Filament\Widgets\ItemsPie;
use App\Filament\Widgets\ItemsTrendLine;
use App\Filament\Widgets\MonthlyStockComparison;
use App\Filament\Widgets\OverallItemsDonut;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopItemsChart;
use App\Filament\Widgets\UserRequestsChart;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class Statistics extends Page
{
    use HasPageShield;


    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.statistics';


    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            ItemsPie::class,
            OverallItemsDonut::class,
            MonthlyStockComparison::class,
            TopItemsChart::class,
            ItemsTrendLine::class,
            UserRequestsChart::class,
            CriticalStatusTrend::class,

        ];
    }
}
