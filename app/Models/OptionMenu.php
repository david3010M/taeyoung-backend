<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="OptionMenu",
 *     title="OptionMenu",
 *     type="object",
 *     required={"id","name", "route","icon","groupmenu_id"},
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Principal"),
 *     @OA\Property(property="route", type="string", example="principal"),
 *     @OA\Property(property="order", type="number", example="1"),
 *     @OA\Property(property="icon", type="string", example="fas fa-user"),
 *     @OA\Property(property="groupmenu_id", type="string", example="1")
 * )
 */
class OptionMenu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'route',
        'order',
        'icon',
        'groupmenu_id'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function groupMenu()
    {
        return $this->belongsTo(GroupMenu::class, 'groupmenu_id');
    }

    public function accesses()
    {
        return $this->hasMany(Access::class, 'optionmenu_id');
    }
}
