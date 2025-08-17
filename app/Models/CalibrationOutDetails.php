<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalibrationOutDetails extends Model
{
    protected $table = 'inv_calibration_out_details';
    protected $fillable = [
        'calibration_out_id',
        'calibration_id',
        'stock_id',
        'item_id',
        'number',
        'date',
        'due_date',
        'file',
        'status'
    ];

    /**
     * Get the calibration out record this detail belongs to.
     */
    public function calibrationOut()
    {
        return $this->belongsTo(CalibrationOut::class);
    }

    public function calibration()
    {
        return $this->belongsTo(Calibration::class);
    }

    public function items()
    {
        return $this->belongsTo(Item::class,  'item_id');
    }
    public function stock()
    {
        return $this->belongsTo(Stock::class,  'stock_id');
    }
}
