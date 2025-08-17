<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    protected $table = 'ws_hr_public_holidays';
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    // Helper to check if a given date falls in this holiday span
    public function includesDate(\Carbon\Carbon $date): bool
    {
        // If no end_date is set, treat as single-day holiday
        return $date->between(Carbon::parse($this->start_date), Carbon::parse($this->end_date ?? $this->start_date));
    }
}
