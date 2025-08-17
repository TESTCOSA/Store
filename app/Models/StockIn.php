<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIn extends Model
{
    protected $table = 'inv_stock_in';
    protected $fillable = [
        'warehouse_id',
        'stocked_by',
        'stocked_date',
        'approved_by',
        'approved',
        'approve_date',
        'status',
    ];


    public function inWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function inUserStocked()
    {
        return $this->belongsTo(UserDetails::class, 'stocked_by');
    }
    public function inUserApproved()
    {
        return $this->belongsTo(UserDetails::class, 'approved_by');
    }
    public function inDetails(): HasMany
    {
        return $this->hasMany(StockInDetails::class);
    }


}
