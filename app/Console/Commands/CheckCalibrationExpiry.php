<?php

namespace App\Console\Commands;

use App\Models\Calibration;
use App\Models\User; // Assuming notifications are sent to users
use App\Notifications\CalibrationExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckCalibrationExpiry extends Command
{
    protected $signature = 'calibration:check-expiry';
    protected $description = 'Check for calibrations that are expired or nearing expiry and notify users.';

    public function handle()
    {
        $today = Carbon::today();
        $calibrations = Calibration::with('item')
            ->where('due_date', '<=', $today->addDays(7)) // Notify if due in 7 days or less
            ->where('due_date', '>=', $today) // Exclude already expired ones
            ->get();

        if ($calibrations->isEmpty()) {
            $this->info('No calibrations nearing expiry.');
            return;
        }

        // Notify users
        $users = User::all(); // Modify this to target specific users
        foreach ($calibrations as $calibration) {
            foreach ($users as $user) {
                $user->notify(new CalibrationExpiryNotification($calibration));
            }
        }

        $this->info('Notifications sent for calibrations nearing expiry.');
    }
}

