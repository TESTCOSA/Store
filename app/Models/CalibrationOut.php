<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalibrationOut extends Model
{
    protected $table = 'inv_calibration_out';
    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'calibration_by',
        'calibration_stock_out_date',
        'user_id',
        'approved_by',
        'approve_date',
        'approved',
        'return_date',
        'status',
    ];

    public function suppliers()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function calibrationBy()
    {
        return $this->belongsTo(UserDetails::class, 'calibration_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(UserDetails::class, 'approved_by');
    }


    public function details()
    {
        return $this->hasMany(CalibrationOutDetails::class, 'calibration_out_id');
    }
}
