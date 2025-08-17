<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $table = 'ws_departments2';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_name_ar',
        'department_en',
        'enabled',
        'parent_id',
    ];
}
