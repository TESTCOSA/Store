<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Imports\AttendanceImporter;
use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\UserDetails;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(AttendanceImporter::class),
        ];
    }
    public function getTabs(): array
    {
        $year = now()->year;

        // "All"
        $tabs = [
            'all' => Tab::make('All Periods'),
        ];

        // 12 payroll‐period tabs
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 21)->subMonth()->startOfDay();
            $end   = Carbon::create($year, $m, 20)->endOfDay();

            $tabs["{$year}-{$m}"] = Tab::make(Carbon::create($year, $m, 1)->format('M').' Payroll')
//                ->modifyQueryUsing(fn(Builder $q) => $q->whereBetween('date', [$start, $end]))
                ->query(fn ($query) => $query->whereBetween('date', [$start, $end]));
        }

        return $tabs;
    }

    public function updateUserIds()
    {
        // Get all attendance records where user_id is null
        $attendances = Attendance::whereNull('user_id')->get();

        foreach ($attendances as $attendance) {
            // Find the corresponding user_id based on emp_code
            $userDetail = UserDetails::where('emp_code', $attendance->emp_code)->first();

            // Update the attendance record if a matching user detail exists
            if ($userDetail) {
                $attendance->user_id = $userDetail->user_id;
                $attendance->save();
            }
        }

        Notification::make()
            ->title('Attendance records updated with user IDs.')
            ->success()
            ->send();

        }
}
