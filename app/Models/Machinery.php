<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Machinery",
 *     type="object",
 *     title="Machinery",
 *     required={"name", "purchasePrice", "salePrice", "unit_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="MCH-001"),
 *     @OA\Property(property="name", type="string", example="Excavadora"),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=50000),
 *     @OA\Property(property="salePrice", type="number", format="float", example=65000),
 *     @OA\Property(property="unit_id", type="integer", example=1),
 *     @OA\Property(
 *         property="unit",
 *         ref="#/components/schemas/UnitResource",
 *         description="Relación con la tabla units"
 *     )
 * )
 */
class Machinery extends Model
{
    use SoftDeletes;

    protected $table = 'machineries';

    protected $fillable = [
        'code',
        'name',
        'purchasePrice',
        'salePrice',
        'unit_id',
    ];

    /**
     * Relación con la tabla units.
     * Cada Machinery pertenece a una Unit.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
