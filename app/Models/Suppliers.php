<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    protected $table = 'inv_supplier';
    protected $fillable = [
        'name',
        'status',
        'address',
    ];
}
