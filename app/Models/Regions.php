<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    protected $table = 'ws_regions';
    protected $primaryKey = 'region_id';
    protected $fillable = [
        'region_id',
        'country_code',
        'region_name_ar',
        'region_name_en',
        'enabled',
        'coordinator_id',
        'supervisor_id',
        'tr_coordinator_id',
        'sales_manager',
        'training_manager',
        'inspection_manager'
    ];


    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}
