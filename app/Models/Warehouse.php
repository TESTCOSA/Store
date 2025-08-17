<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'inv_warehouses';
    protected $fillable = [
        'region_id',
        'name',
        'address',
    ];



    public function regions()
    {
        return $this->belongsTo(Regions::class, 'region_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockIn()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOut()
    {
        return $this->hasMany(StockOut::class);
    }
    public function stock()
    {
        return $this->hasMany(Stock::class, 'warehouse_id');
    }

    public function missingItems()
    {
        return $this->hasMany(MissingItems::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasManyThrough(
            Item::class,   // Final model you want to access
            Stock::class,  // Intermediate model
            'warehouse_id', // Foreign key on the stocks table
            'id',           // Local key on the items table (usually the primary key)
            'id',           // Local key on the warehouses table
            'item_id'       // Foreign key on the stocks table that links to the items table
        );
    }

}
