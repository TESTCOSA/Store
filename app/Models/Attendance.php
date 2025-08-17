<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'ws_hr_attendances';
    protected $fillable = [
        'user_id',
        'emp_code',
        'date',
        'clock_in',
        'clock_out'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetails::class, 'user_id');
    }
}
