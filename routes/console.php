<?php

use App\Checker;
use App\Console\Commands\CheckCalibrationStatus;
use App\Console\Commands\SendCalibrationNotifications;
use App\Jobs\CheckExpiredCalibrations;
use App\Jobs\SendExpiredCalibrationNotifications;
//use Illuminate\Console\Scheduling\Schedule;
use App\Mail\CalibrationExpired;
use App\Models\Calibration;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;




Artisan::command('calibration:check', function () {
    $this->call(CheckCalibrationStatus::class);
});

Artisan::command('calibration:notify', function () {
    $this->call(SendCalibrationNotifications::class);
});
