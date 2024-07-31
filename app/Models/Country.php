<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema (
 *     schema="Country",
 *     title="Country",
 *     description="Country model",
 *     @OA\Property( property="id", type="integer", example="1"),
 *     @OA\Property( property="name", type="string", example="Peru")
 * )
 */
class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = ['name'];
}
