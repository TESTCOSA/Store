<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\CrownBlock\Checklist as CrownBlockChecklist;

class ChecklistDetail extends Model
{
    protected $table = 'ws_checklists_details';

    protected $primaryKey = 'item_id';

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'item_title',
        'item_title_ar',
        'reference',
        'safety',
        'checklist_id',
        'sortorder',
    ];

    // Optional: if you want relationship to checklist
    public function checklist()
    {
        return $this->belongsTo(CrownBlockChecklist::class, 'checklist_id');
    }
}
