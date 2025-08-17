<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $table = 'ws_customers';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'customer_name',
        'category_id',
        'work_field',
        'mobile',
        'email',
        'phone',
        'address',
        'city',
        'focus_code',
        'due_days',
        'coordinator_name',
        'coordinator_job',
        'website',
        'city_id',
        'region_id',
        'country_id',
        'enabled',
        'extension',
        'customer_code',
        'vat_number',
        'cards_notes_email',
        'certs_notes_email',
        'org_code',
    ];

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'customer_id');
    }

}
