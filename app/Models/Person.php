<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Supplier",
 *     title="Supplier",
 *     description="Supplier model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="type", type="string", example="JURIDICA"),
 *     @OA\Property(property="ruc", type="string", example="20547869541"),
 *     @OA\Property(property="businessName", type="string", example="Distribuidora de Productos S.A."),
 *     @OA\Property(property="address", type="string", example="Jr. Los Pinos 123"),
 *     @OA\Property(property="email", type="string", example="supplier@gmail.com"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="representativeDni", type="string", example="12345678"),
 *     @OA\Property(property="representativeNames", type="string", example="Juan Perez"),
 *     @OA\Property(property="country_id", type="integer", example="1"),
 *     @OA\Property(property="country", type="object", ref="#/components/schemas/Country")
 * )
 */
class Person extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'ruc',
        'businessName',
        'address',
        'email',
        'phone',
        'representativeDni',
        'representativeNames',
        'country_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }


}
