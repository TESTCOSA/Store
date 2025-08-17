<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $table = 'ws_user_groups';

    protected $fillable = [
        'group_name_ar',
        'group_name_en',
        'enabled'
    ];

}
