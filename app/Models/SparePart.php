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
 *     @OA\Property(property="stock", type="integer", example="10" )
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
    ];

    protected $casts = [
        'purchasePrice' => 'float',
        'salePrice' => 'float',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
