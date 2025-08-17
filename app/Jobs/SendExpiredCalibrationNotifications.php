<?php

namespace App\Jobs;

use App\Mail\CalibrationExpired;
use App\Models\Calibration;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendExpiredCalibrationNotifications implements ShouldQueue
{
    use Dispatchable, Queueable;
    public function handle()
    {
        $expiredCalibrations = Calibration::where('status', 0)
            ->where('due_date', '<', now())
            ->get();

        $roleHolders = User::role('store_keeper')->get(); // Use Spatie Roles or appropriate logic

        foreach ($roleHolders as $user) {
            Mail::to($user->email)->sendNow(new CalibrationExpired($expiredCalibrations));
        }
    }
}
