<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'inv_types';
    protected $fillable = [
        'name',
        'is_calibrated',
        'is_maintained',
        'is_returned',
        'is_consumable',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class, 'types_id');
    }


}
