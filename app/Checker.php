<?php

namespace App;

use App\Mail\CalibrationExpired;
use App\Models\Calibration;
use App\Models\Stock;
use Illuminate\Support\Facades\Mail;

class Checker
{
    public function __invoke()
    {
        $stockItems = Stock::all();

        foreach ($stockItems as $stock)
        $calibrations = Calibration::where('due_date', '<=', now()->addDays(14))
            ->where('status', '!=', 0)
            ->get();
        if ($calibrations->count() > 0) {
           return Mail::to('m.alarmani@testcosa.com')->send(new CalibrationExpired($calibrations));
        }
    }



}
