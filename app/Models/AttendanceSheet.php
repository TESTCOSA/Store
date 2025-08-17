<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSheet extends Model
{
    protected $table = 'ws_hr_attendance_sheets';

    protected $fillable = [
        'approval_id',
        'user_id',
        'department_id',
        'notes',
        'period_start',
        'period_end',
        'period_label',
        'attendance_data',
        'present_count',
        'absent_count',
        'annual_leave_count',
        'sick_leave_count',
        'emergency_leave_count',
        'public_holiday_count',
        'unpaid_leave_count',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'attendance_data' => 'array',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }



    public function userDetails(): BelongsTo
    {
        return $this->belongsTo(UserDetails::class, 'user_id');
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(AttendanceApproval::class, 'approval_id');
    }

    public function department()
    {
        return $this->hasOne(Departments::class, 'department_id', 'department_id');
    }
}
