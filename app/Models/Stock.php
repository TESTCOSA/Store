<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'inv_stocks';
    protected $fillable = [
        'item_id',
        'quantity',
        'available_quantity',
        'warehouse_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function stockIns()
    {
        return $this->hasMany(StockInDetails::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOutDetails::class, 'stock_id');
    }
}
