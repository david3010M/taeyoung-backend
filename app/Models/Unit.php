<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Unit",
 *     title="Unit",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Unidad"),
 *     @OA\Property(property="abbreviation", type="string", example="UN")
 * )
 */
class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    const filters = [
        'name' => 'like',
        'abbreviation' => 'like'
    ];

    const sorts = [
        'id',
        'name',
        'abbreviation'
    ];

    public function spareParts()
    {
        return $this->hasMany(SparePart::class);
    }
}
