<?php

namespace App\Console\Commands;

use App\Models\Calibration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCalibrationNotifications extends Command
{
    protected $signature = 'calibration:notify';
    protected $description = 'Send calibration expiration notifications';

    public function handle()
    {
        $expiringSoon = Calibration::whereBetween('due_date', [
            Carbon::now(),
            Carbon::now()->addWeeks(2)
        ])->with('item')->get();

        if ($expiringSoon->isNotEmpty()) {
            $recipients = User::role(['store_keeper', 'admin'])
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            Mail::to($recipients)->send(new \App\Mail\CalibrationExpiration($expiringSoon));
        }

        $this->info('Calibration notifications sent successfully.');
    }

}
