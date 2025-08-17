<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quarantine extends Model
{
    protected $table = 'inv_quarantines';
    protected $fillable = [
        'stock_id',
        'item_id',
        'warehouse_id',
        'user_id',
        'reason',
        'status',
        'quarantined_at',
        'released_at',
        'released_by',
    ];

    /**
     * Relationship with the Item model.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(UserDetails::class, 'user_id');
    }
    public function releasedBy()
    {
        return $this->belongsTo(UserDetails::class, 'released_by');
    }
}
