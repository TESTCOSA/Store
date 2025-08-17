<?php

namespace App\Models\TravelingBlock;

use App\Models\UserDetails;
use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    protected $table = 'ws_certifications_traveling_block';
    protected $primaryKey = 'certification_id';
    public $timestamps = false;

    protected $fillable = [
        'wo_id', 'test_date', 'next_test_date', 'customer_id', 'checklist_id',
        'work_location', 'standard_type_api', 'standard_type_astm', 'standard_name_api',
        'standard_name_astm', 'inspection_method', 'mpi_type', 'insp_type', 'mg_eq_used',
        'mg_eq_manuf', 'magnet_no', 'manuf_date', 'model', 'description', 'manufacturer',
        'sheaves_od', 'drill_line_dia', 'rated_loading', 'equipment_no', 'sheaves_sn',
        'sheaves_sn_disc', 'contrast_media', 'contrast_media_batch', 'contrast_media_manuf',
        'indicator', 'indicator_batch', 'indicator_manuf', 'cal_test_weight', 'pole_spacing',
        'light_meter_no', 'light_intensity_value', 'surface_condition', 'temprature',
        'sheave_gauge_no', 'caliper_no', 'mpi_results', 'dim_results', 'inspector_id',
        'approved_by', 'date_added', 'qr_code', 'certification_file', 'traveling_photo',
        'sheave_wear_photo', 'sheave_groove_photo', 'approved', 'approved_date',
        'reject_reason', 'sequence', 'rejected_times', 'status', 'readings', 'uploaded'
    ];

    public function inspector()
    {
        return $this->belongsTo(UserDetails::class, 'inspector_id');
    }

    public function approver()
    {
        return $this->belongsTo(UserDetails::class, 'approved_by');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class, 'certification_id', 'certification_id');
    }

    public function readings()
    {
        return $this->hasMany(Reading::class, 'certification_id', 'certification_id');
    }

    public function workOrder()
    {
        return $this->hasMany(WorkOrder::class, 'wo_id', 'wo_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }



    public function checklist()
    {
        return $this->hasOne(Checklist::class, 'cert_id', 'certification_id');
    }
}

