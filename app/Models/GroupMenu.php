<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMenu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'order',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function optionMenus()
    {
        return $this->hasMany(OptionMenu::class, 'groupmenu_id');
    }
}
