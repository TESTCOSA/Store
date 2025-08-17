<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveOrders extends Model
{

    protected $table = 'ws_hr_leave_orders';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'date_updated';

    protected $fillable = [
        'leave_type',
        'emp_id',
        'leave_with_salary',
        'start_date',
        'end_date',
        'days_count',
        'start_work_date',
        'leave_status',
        'in_out_country',
        'contact_address',
        'contact_number',
        'turnover_tasks',
        'alternative_emp_id',
        'alt_emp_agree',
        'alt_emp_agree_date',
        'sup_emp_id',
        'sup_emp_agree',
        'sup_agree_date',
        'sup_agree_notes',
        'hr_emp_id',
        'hr_emp_agree',
        'hr_agree_date',
        'hr_agree_notes',
        'mg_emp_id',
        'mg_emp_agree',
        'mg_agree_date',
        'mg_agree_notes',
        'leave_file',
        'tickets_file',
        'medical_file',
        'admin_view',
        'added_by',
        'reject_reason',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'start_work_date'  => 'date',
        'alt_emp_agree_date' => 'datetime',
        'sup_agree_date'   => 'datetime',
        'hr_agree_date'    => 'datetime',
        'mg_agree_date'    => 'datetime',
        'date_created'     => 'datetime',
        'date_updated'     => 'datetime',
    ];

    // Define relationships as needed, e.g.:
     public function leaveType()
     {
         return $this->belongsTo(LeaveTypes::class, 'leave_type');
     }

    public function user()
    {
        return $this->belongsTo(UserDetails::class, 'emp_id');
    }


}
