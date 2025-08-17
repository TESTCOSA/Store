<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'inv_maintenance';

    protected $fillable = [

        'warehouse_id',
        'supplier_id',
        'stock_id',
        'maintenance_by',
        'maintenance_stock_out_date',
        'approved_by',
        'approve_date',
        'pass',
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

    public function maintenanceBy()
    {
        return $this->belongsTo(UserDetails::class, 'calibration_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(UserDetails::class, 'approved_by');
    }


    public function details()
    {
        return $this->hasMany(MaintenanceDetails::class, 'maintenance_id');
    }
}
