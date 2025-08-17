<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveTypes extends Model
{
    protected $table = 'ws_hr_leave_types';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'min_days',
        'max_days',
        'leave_rule',
        'enabled',
        'leave_rate',
        'reg_leave',
        'in_balance',
    ];

    protected $casts = [
        'min_days'    => 'integer',
        'max_days'    => 'float',
        'leave_rule'  => 'float',
        'leave_rate'  => 'float',
    ];

     public function orders()
     {
         return $this->hasMany(LeaveOrders::class, 'leave_type');
     }

}
