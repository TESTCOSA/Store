<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInDetails extends Model
{

    protected $table = 'inv_stock_in_details';

    protected $fillable = [
        'stock_in_id',
        'supplier_id',
        'stock_id',
        'item_id',
        'quantity',
        'notes'
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class, 'stock_in_id');
    }
    public function inStock()
    {
        return $this->belongsTo(Stock::class,'stock_id');
    }

    public function inItems()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function inSupplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

}
