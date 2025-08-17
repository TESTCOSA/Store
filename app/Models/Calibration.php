<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calibration extends Model
{
    use HasFactory;
    protected $table = 'inv_calibrations';

    protected $fillable = [
        'item_id',
        'date',
        'due_date',
        'number',
        'file',
        'status',
    ];
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function calibrationOut()
    {
        return $this->hasMany(CalibrationOutDetails::class, 'calibration_id');
    }



    public static function checkForExpiredCalibrations()
    {
        $expiredCalibrations = self::where('due_date', '<', now()->addDays(14))->where('status', '!=', 0)->get();

        foreach ($expiredCalibrations as $calibration) {
            $calibration->update([
                'status' => 0,
            ]);
            $calibration->item->update([
                'status' => 3,
            ]);
        }
        return $expiredCalibrations;
    }
}

