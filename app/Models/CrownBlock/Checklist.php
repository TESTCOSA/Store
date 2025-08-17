<?php

namespace App\Models\CrownBlock;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = 'ws_crown_block_certifications_checklists';
    public $timestamps = false;

    protected $fillable = [
        'eq_type',
        'wo_id',
        'cert_id',
        'customer_id',
        'checklist_id',
        'inspector_id',
        'checklist_date',
        'user_id',
        'date_added',
        'checklist_file',
        'pass_fail',
    ];

    public function crownBlock()
    {
        return $this->belongsTo(Main::class, 'cert_id', 'certification_id');
    }

    public function details()
    {
        return $this->hasMany(ChecklistDetail::class, 'cert_id', 'cert_id')
            ->orderBy('checklist_item_id');
    }
}
