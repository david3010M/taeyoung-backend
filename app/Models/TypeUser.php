<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="TypeUser",
 *     title="TypeUser",
 *     description="TypeUser model",
 *     @OA\Property ( property="id", type="integer", example="1" ),
 *     @OA\Property ( property="name", type="string", example="Admin" )
 * )
 */
class TypeUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'name' => 'like',
    ];

    const sorts = [
        'id',
        'name',
    ];

    public function access()
    {
        return $this->hasMany(Access::class, 'typeuser_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'typeuser_id');
    }

    public function setAccess($id, array $optionMenuIds)
    {
        $currentOptionMenuIds = $this->access()->pluck('optionmenu_id')->toArray();

        $toAdd = array_diff($optionMenuIds, $currentOptionMenuIds);
        $toRemove = array_diff($currentOptionMenuIds, $optionMenuIds);

        if (!empty($toRemove)) {
            $this->access()->whereIn('optionmenu_id', $toRemove)->forceDelete();
        }

        // Añadir los nuevos accesos que no existen actualmente
        foreach ($toAdd as $optionMenuId) {
            if (!in_array($optionMenuId, $currentOptionMenuIds)) {
                Access::create([
                    'typeuser_id' => $id,
                    'optionmenu_id' => $optionMenuId,
                ]);
            }
        }
    }

}
