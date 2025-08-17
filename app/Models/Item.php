<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $table = 'inv_items';
    protected $fillable = [
        'category_id',
        'name',
        'size',
        'serial_number',
        'test_tag',
        'make',
        'model',
        'status',
        'sequence',
        'low_stock',
        'file',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'item_id');
    }

    public function stockInDetails()
    {
        return $this->hasMany(StockInDetails::class);
    }

    public function stockOutDetails()
    {
        return $this->hasMany(StockOutDetails::class);
    }

    public function calibrations()
    {
        return $this->hasOne(Calibration::class);
    }

    public function missingItems()
    {
        return $this->hasMany(MissingItems::class, 'item_id');
    }
}

