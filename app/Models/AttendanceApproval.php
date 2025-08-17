<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceApproval extends Model
{
    use HasFactory;

    protected $table = 'ws_hr_attendance_approvals';
    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'period_label',
        'department',
        'manager_approval',
        'manager_approved_at',
        'manager_comments',
        'hr_approval',
        'hr_approved_at',
        'hr_comments',
        'accountant_approval',
        'accountant_approved_at',
        'accountant_comments',
        'director_approval',
        'director_approved_at',
        'director_comments',
        'status',
    ];
    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'manager_approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
        'accountant_approved_at' => 'datetime',
        'director_approved_at' => 'datetime',
        'manager_approval' => 'boolean',
        'hr_approval' => 'boolean',
        'accountant_approval' => 'boolean',
        'director_approval' => 'boolean',
    ];
    public function sheets(): HasMany
    {
        return $this->hasMany(AttendanceSheet::class, 'approval_id');
    }
}
