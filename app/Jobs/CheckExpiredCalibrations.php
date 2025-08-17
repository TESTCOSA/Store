<?php

namespace App\Jobs;

use App\Models\Calibration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CheckExpiredCalibrations implements ShouldQueue
{
    use Dispatchable, Queueable;
    public function handle()
    {
        Calibration::checkForExpiredCalibrations();
    }
}
