<?php

namespace App\Filament\Resources\AttendanceSheetResource\Pages;

use App\Filament\Resources\AttendanceSheetResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceSheet extends EditRecord
{
    protected static string $resource = AttendanceSheetResource::class;



    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();


        $existingAttendance =  $data['attendance_data'] ?? [];
        $updatedAttendance = [];
        $present = 0;
        $absent = 0;
        $annual = 0;
        $sick = 0;
        $emergency = 0;
        $public = 0;
        $unpaid = 0;

        // Get dates from form data (shouldn't change on edit ideally)
        $periodStart = Carbon::parse($data['period_start']);
        $periodEnd = Carbon::parse($data['period_end']);
        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;

        // Merge existing attendance with submitted changes
        for ($day = 1; $day <= $daysInPeriod; $day++) {
            $key = "day_{$day}";
            if (isset($data[$key])) {
                $updatedAttendance[$key] = $data[$key];
            } else {
                $updatedAttendance[$key] = $existingAttendance[$key] ?? (in_array(Carbon::parse($periodStart)->addDays($day - 1)->format('D'), ['Fri', 'Sat']) ? 'PH' : 'A');
            }

            // Count statuses based on the merged/updated data
            $status = $updatedAttendance[$key] ?? 'A';
            switch ($status) {
                case 'A':
                    $present++;
                    break;
                case '-':
                case 'X': // Consider 'X' as absent as well, if it exists
                    $absent++;
                    break;
                case 'AL':
                    $annual++;
                    break;
                case 'SL':
                    $sick++;
                    break;
                case 'EL':
                    $emergency++;
                    break;
                case 'PH':
                    $public++;
                    break;
                case 'UL':
                    $unpaid++;
                    break;
            }
            unset($data[$key]); // Remove individual day entries from $data
        }

        $data['attendance_data'] = $updatedAttendance;
        $data['present_count'] = $present;
        $data['absent_count'] = $absent;
        $data['annual_leave_count'] = $annual;
        $data['sick_leave_count'] = $sick;
        $data['emergency_leave_count'] = $emergency;
        $data['public_holiday_count'] = $public;
        $data['unpaid_leave_count'] = $unpaid;


        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
