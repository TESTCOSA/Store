<?php

namespace App\Models;

use App\Models\CrownBlock\Main;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $table = 'ws_ass_tr_wo';
    protected $primaryKey = 'wo_id';
    public $timestamps = false;

    protected $fillable = [
        'wo_hash',
        'sticker_type',
        'q_id',
        'customer_id',
        'sub_customer_id',
        'customer_alt_name',
        'qr_id',
        'wo_date',
        'department_name',
        'po_no',
        'po_file',
        'cost_center',
        'contact_p_location',
        'coor_email',
        'coor_mobile',
        'work_location',
        'location_details',
        'coordinator_id',
        'date_added',
        'gate_pass',
        'car_id',
        'notes',
        'wo_file',
        'approved',
        'approved_by',
        'approved_date',
        'status',
        'sequence',
        'distance',
        'driver_id',
        'eiac_wo',
        'customer_sign',
        'customer_name',
        'customer_badge',
        'customer_mobile',
        'close_code',
        'collected_code',
        'wo_type',
        'reports_uploaded',
        'payment_type',
        'timesheet_file',
        'invoice_file',
        'invoice_total',
        'coc_file',
        'coc_link',
    ];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function assigned(): HasMany
    {
        return $this->hasMany(WorkOrderAssigned::class, 'wo_id');
    }
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function crownBlock(): HasMany
    {
        return $this->hasMany(Main::class, 'wo_id');
    }
}
