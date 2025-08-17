<?php

namespace App\Filament\Resources\AttendanceSheetResource\Pages;

use App\Filament\Resources\AttendanceSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateAttendanceSheet extends CreateRecord
{
    protected static string $resource = AttendanceSheetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract attendance data
        $attendance = collect($data)
            ->filter(fn ($_, $key) => str_starts_with($key, 'day_'))
            ->all();

        // Initialize counters
        $present = 0;
        $absent = 0;
        $annual = 0;
        $sick = 0;
        $public = 0;
        $unpaid = 0;

        // Count occurrences of each status
        foreach ($attendance as $status) {
            switch ($status) {
                case 'A':
                    $present++;
                    break;
                case 'X':
                case '-':
                    $absent++;
                    break;
                case 'AL':
                    $annual++;
                    break;
                case 'SL':
                    $sick++;
                    break;
                case 'PH':
                    $public++;
                    break;
                case 'UL':
                    $unpaid++;
                    break;
                case 'DO':
                    // Count as day off, not as absent
                    break;
            }
        }

        // Save attendance JSON to attendance_data
        $data['attendance_data'] = json_encode($attendance);
        $data['period_label'] = Carbon::parse($data['period_end'])->format('F').' - '.'Payroll';

        // Save the counts
        $data['present_count'] = $present;
        $data['absent_count'] = $absent;
        $data['annual_leave_count'] = $annual;
        $data['sick_leave_count'] = $sick;
        $data['public_holiday_count'] = $public;
        $data['unpaid_leave_count'] = $unpaid;

        return $data;
    }
}
