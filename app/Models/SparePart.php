<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="SparePartRequest",
 *     title="SparePartRequest",
 *     required={"code", "name", "purchasePrice", "salePrice", "stock"},
 *     @OA\Property(property="code", type="string", example="123456" ),
 *     @OA\Property(property="name", type="string", example="Spare Part" ),
 *     @OA\Property(property="purchasePrice", type="number", example="100.00" ),
 *     @OA\Property(property="salePrice", type="number", example="150.00" ),
 * )
 *
 */
class SparePart extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'purchasePrice',
        'salePrice',
        'stock',
        'unit_id',
    ];

    protected $casts = [
        'purchasePrice' => 'decimal:2',
        'salePrice' => 'decimal:2',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'code' => 'like',
        'name' => 'like',
    ];

    const sorts = [
        'id',
        'code',
        'name',
        'purchasePrice',
        'salePrice',
        'stock',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

}
