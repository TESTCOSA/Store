<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutDetails extends Model
{
    protected $table = 'inv_stock_out_details';
    protected $fillable = ['stock_out_id','stock_id', 'item_id', 'quantity', 'note', 'returned'];


    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }
    public function outStock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
    public function outItems()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }








}
