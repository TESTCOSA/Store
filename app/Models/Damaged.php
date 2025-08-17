<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Damaged extends Model
{
    protected $table = 'inv_damaged';
    protected $fillable = [
        'stock_out_id',
        'item_id',
        'stock_id',
        'damaged_by',
        'work_order_id',
        'reported_by',
        'warehouse_id',
        'status',
        'resolved_by',
        'reported_at',
        'resolved_at',
        'description',
    ];




    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Relationship with the Stock model.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class, 'stock_out_id');
    }

    public function reporter()
    {
        return $this->belongsTo(UserDetails::class, 'reported_by');
    }
    public function damagedBy()
    {
        return $this->belongsTo(UserDetails::class, 'damaged_by');
    }

    /**
     * Relationship with the User model for the resolver.
     */
    public function resolver()
    {
        return $this->belongsTo(UserDetails::class, 'resolved_by');
    }

    /**
     * Relationship with the Warehouse model.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
