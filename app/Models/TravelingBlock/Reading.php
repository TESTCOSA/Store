<?php

namespace App\Models\TravelingBlock;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    protected $table = 'ws_certifications_traveling_block_reading';
    public $timestamps = false;

    protected $fillable = [
        'certification_id',
        'sheaves_sn',
        'groove_a',
        'groove_b',
        'groove_c',
        'groove_d',
        'pass_fail',
    ];

    public function travelingBlock()
    {
        return $this->belongsTo(Main::class, 'certification_id', 'certification_id');
    }
}

