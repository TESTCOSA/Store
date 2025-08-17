<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceDetails extends Model
{

    protected $table = 'inv_maintenance_details';

    protected $fillable = [
        'maintenance_id',
        'stock_id',
        'item_id',
        'quantity',
    ];


    /**
     * Get the calibration out record this detail belongs to.
     */
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
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
