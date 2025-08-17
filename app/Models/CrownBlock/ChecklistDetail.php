<?php

namespace App\Models\CrownBlock;

use Illuminate\Database\Eloquent\Model;

class ChecklistDetail extends Model
{
    protected $table = 'ws_crown_block_certifications_checklists_details';
    public $timestamps = false;

    protected $fillable = [
        'cert_id',
        'checklist_id',
        'checklist_item_id',
        'pass_fail',
    ];

    public function crownBlock()
    {
        return $this->belongsTo(Main::class, 'cert_id', 'certification_id');
    }

    public function item()
    {
        // Links this model to the main ChecklistDetail model to get the title
        return $this->belongsTo(\App\Models\ChecklistDetail::class, 'checklist_item_id', 'item_id');
    }


}
