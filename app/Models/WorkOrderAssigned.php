<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkOrderAssigned extends Model
{
    protected $table = 'ws_ass_tr_wo_assigned';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'wo_id',
        'user_id',
        'service_id',
        'service_type',
        'quantity',
        'from_date',
        'to_date',
        'timesheet_file',
        'notes',
        'car_id',
        'inspection_type',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'wo_id');
    }

    public function user(): hasOne
    {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }
}
