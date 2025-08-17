<?php

namespace App\Filament\Imports;

use App\Models\Attendance;
use App\Models\UserDetails;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AttendanceImporter extends Importer
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [

        ImportColumn::make('emp_code')
                ->requiredMapping(),
            ImportColumn::make('date')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('clock_in'),
            ImportColumn::make('clock_out'),
        ];
    }

    public function resolveRecord(): ?Attendance
    {
        $attendance = new Attendance();

        if (isset($this->data['date'])) {
            try {
                $formattedDate = \Carbon\Carbon::createFromFormat('d/m/Y', $this->data['date'])->format('Y-m-d');
                $attendance->date = $formattedDate;
            } catch (\Exception $e) {

                $attendance->date = null;
            }
        }
        $attendance->emp_code = $this->data['emp_code'] ?? null;
        $attendance->clock_in = $this->data['clock_in'] ?? null;
        $attendance->clock_out = $this->data['clock_out'] ?? null;

        return $attendance;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your attendance import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }
        return $body;
    }
}
