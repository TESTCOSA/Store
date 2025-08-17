<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class UserDetails extends Model
{
    protected $table = 'ws_user_details';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'full_name_ar',
        'full_name_en',
        'company_id',
        'gender',
        'eiac',
        'saso',
        'mobile',
        'phone_ext',
        'emp_code',
        'email',
        'department_id',
        'job_title_id',
        'nationality',
        'address',
        'pic',
        'driving_license_expire',
        'emp_status',
        'digital_sig',
        'annual_leave_type',
        'sup_emp_id',
        'hire_date',
        'birth_date',
        'leave_balance',
        'digital_sig_b',
        'badge_no',
        'region_id',
        'tot_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function department()
    {
        return $this->hasOne(Departments::class, 'department_id', 'department_id');
    }

    public function userGroups()
    {
        return $this->hasMany(UserGroups::class, 'user_id', 'user_id');
    }

    public function stockIn()
    {
        return $this->hasMany(StockIn::class, 'stocked_by', 'user_id');
    }

    public function stockOut()
    {
        return $this->hasMany(StockOut::class, 'request_by', 'user_id');
    }

    public function allStockOut()
    {
        return $this->hasMany(StockOut::class, 'request_by', 'user_id')
            ->whereIn('status', [1, 2]);
    }
      public function ActiveStockOut()
        {
            return $this->hasMany(StockOut::class, 'request_by', 'user_id')
                        ->whereIn('status', [0, 2]) // 0: Not Returned, 2: Partially Returned
                        ->where('approved', 1);     // Only approved requests
        }

    public function missing()
    {
        return $this->hasMany(MissingItems::class, 'missing_by', 'user_id');
    }
    public function damaged()
    {
        return $this->hasMany(Damaged::class, 'damaged_by', 'user_id');
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id');
    }

    public function calibrationOut()
    {
        return $this->hasMany(CalibrationOut::class, 'calibration_by', 'user_id');
    }

      public function getHasUnresolvedMissingItemsAttribute(): bool
    {
        $hasMissingItems = $this->missing()->where('status', 0)->exists();
        $hasActiveStock = $this->ActiveStockOut()->exists(); // Uses the modified function

        return !($hasMissingItems || $hasActiveStock);
    }
// In App\Models\UserDetails.php

    public function stockOutItems()
    {
        return $this->hasManyThrough(
            \App\Models\StockOutDetails::class, // Final model (requested items details)
            \App\Models\StockOut::class,        // Intermediate model (the request)
            'request_by',                       // Foreign key on StockOut linking to UserDetails
            'stock_out_id',                     // Foreign key on StockOutDetails linking to StockOut
            'user_id',                          // Local key on UserDetails
            'id'                                // Local key on StockOut
        );
    }


//    public function outD(): HasManyThrough
//    {
//        return $this->hasManyThrough(
//            StockOutDetails::class, // The final model
//            StockOut::class,        // The intermediate model
//            'request_by',           // Foreign key on the StockOut table (linking it to UserDetails)
//            'stock_out_id',         // Foreign key on the StockOutDetails table (linking it to StockOut)
//            'user_id',              // Local key on the UserDetails table
//            'id'                    // Local key on the StockOut table
//        );
//    }


}
