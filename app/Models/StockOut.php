<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $table = 'inv_stock_out';
    protected $fillable = [
        'wo_id',
        'warehouse_id',
        'request_by',
        'request_date',
        'approved_by',
        'approved',
        'supervisor_approve',
        'supervisor_id',
        'supervisor_approve_date',
        'approve_date',
        'return_date',
        'status',
    ];




    public function outWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
    public function outDetails()
    {
        return $this->hasMany(StockOutDetails::class);
    }

    public function outUserRequested()
    {
        return $this->belongsTo(UserDetails::class, 'request_by');
    }
    public function outUserApproved()
    {
        return $this->belongsTo(UserDetails::class, 'approved_by');
    }
}
