<?php

namespace App\Imports;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip rows with missing essential data
            if (empty($row['emp_code']) || empty($row['date'])) {
                continue;
            }

            // Parse the date
            try {
                $date = Carbon::parse($row['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Try different date format if the first parse fails
                try {
                    $date = Carbon::createFromFormat('d/m/Y', $row['date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Skip this row if date can't be parsed
                    continue;
                }
            }

            // Create or update attendance record
            Attendance::updateOrCreate(
                [
                    'emp_code' => $row['emp_code'],
                    'date' => $date,
                ],
                [
                    'clock_in' => $row['clock_in'] ?? null,
                    'clock_out' => $row['clock_out'] ?? null,
                ]
            );
        }
    }
}
