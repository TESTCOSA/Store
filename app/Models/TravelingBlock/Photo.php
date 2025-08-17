<?php

namespace App\Models\TravelingBlock;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'ws_certifications_traveling_block_photos';
    public $timestamps = false;

    protected $fillable = [
        'certification_id',
        'file_name',
    ];

    public function travelingBlock()
    {
        return $this->belongsTo(Main::class, 'certification_id', 'certification_id');
    }
}

