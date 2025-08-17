<?php

namespace App\Models\CrownBlock;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'ws_certifications_crown_block_photos';
    public $timestamps = false;

    protected $fillable = [
        'certification_id',
        'file_name',
    ];

    public function crownBlock()
    {
        return $this->belongsTo(Main::class, 'certification_id', 'certification_id');
    }
}
