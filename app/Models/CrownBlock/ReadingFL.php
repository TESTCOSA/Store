<?php

namespace App\Models\CrownBlock;

use Illuminate\Database\Eloquent\Model;

class ReadingFL extends Model
{
    protected $table = 'ws_certifications_crown_block_fast_line_reading';
    public $timestamps = false;

    protected $fillable = [
        'certification_id',
        'fast_line_sn',
        'groove_a',
        'groove_b',
        'groove_c',
        'groove_d',
        'fast_line_photo',
        'pass_fail',
    ];

    public function crownBlock()
    {
        return $this->belongsTo(Main::class, 'certification_id', 'certification_id');
    }
}
