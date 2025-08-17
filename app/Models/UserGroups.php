<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroups extends Model
{
    protected $table = 'ws_user_group';

    protected $fillable = [
        'user_id',
        'group_id',
    ];

    public function user()
    {
        return $this->belongsTo(UserDetails::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

}
