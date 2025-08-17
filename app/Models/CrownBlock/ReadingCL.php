<?php

namespace App\Models\CrownBlock;

use Illuminate\Database\Eloquent\Model;

class ReadingCL extends Model
{
    protected $table = 'ws_certifications_crown_block_cluster_reading';
    public $timestamps = false;

    protected $fillable = [
        'certification_id',
        'cluster_sn',
        'groove_a',
        'groove_b',
        'groove_c',
        'groove_d',
        'cluster_photo',
        'pass_fail',
    ];

    public function crownBlock()
    {
        return $this->belongsTo(Main::class, 'certification_id', 'certification_id');
    }
}
