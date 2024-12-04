<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="GroupMenu",
 *     title="GroupMenu",
 *     type="object",
 *     required={"id","name", "icon"},
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Admin"),
 *     @OA\Property(property="icon", type="string", example="fas fa-user"),
 *     @OA\Property(property="order", type="number", example="1"),
 *     @OA\Property(property="option_menus", type="array", @OA\Items(ref="#/components/schemas/OptionMenu"))
 * )
 */
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
        'deleted_at',
    ];

    public function optionMenus()
    {
        return $this->hasMany(OptionMenu::class, 'groupmenu_id');
    }

    public static function getFilteredGroupMenus($userTypeId)
    {
        return self::with(['optionMenus' => function ($query) use ($userTypeId) {
            $query->whereHas('accesses', function ($query) use ($userTypeId) {
                $query->where('typeuser_id', $userTypeId);
            });
        }])
            ->get()
            ->map(function ($groupMenu) use ($userTypeId) {
                // Filtrar optionMenus según el acceso del usuario
                $groupMenu->option_menus = $groupMenu->optionMenus->filter(function ($optionMenu) use ($userTypeId) {
                    return $optionMenu->accesses->contains('typeuser_id', $userTypeId);
                })->values();
                // Eliminar 'accesses' de los optionMenus filtrados
                $groupMenu->option_menus->each(function ($optionMenu) {
                    unset($optionMenu->accesses);
                });
                // Ocultar el atributo 'optionMenus' original
                unset($groupMenu->optionMenus);
                // Eliminar las opciones de menu que tenga id 7,8,9,10
                $groupMenu->option_menus = $groupMenu->option_menus->filter(function ($optionMenu) {
                    return !in_array($optionMenu->id, [7, 8, 9, 10, 11, 12, 13]);
                });
                return $groupMenu;
            });
    }
}
