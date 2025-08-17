<?php

namespace App\Console\Commands;

use App\Models\Calibration;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckCalibrationStatus extends Command
{
    protected $signature = 'calibration:check';
    protected $description = 'Check and update calibration statuses';

    public function handle()
    {
        // Update status for items expiring within 2 weeks
        $threshold = Carbon::now()->addWeeks(2);

        Calibration::where('due_date', '<=', $threshold)
            ->where('status', '!=', 'expired')
            ->with('item')
            ->chunk(200, function ($calibrations) {
                foreach ($calibrations as $calibration) {
                    $calibration->item()->update(['status' => 3]); // 3 = In Calibration
                }
            });

        $this->info('Calibration statuses updated successfully.');
    }
}
