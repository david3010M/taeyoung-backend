<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Supplier",
 *     title="Supplier",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="typeDocument", type="string", example="DNI"),
 *     @OA\Property(property="dni", type="string", example="12345678"),
 *     @OA\Property(property="ruc", type="string", example="12345678901"),
 *     @OA\Property(property="names", type="string", example="Juan"),
 *     @OA\Property(property="fatherSurname", type="string", example="Perez"),
 *     @OA\Property(property="motherSurname", type="string", example="Gomez"),
 *     @OA\Property(property="businessName", type="string", example="Empresa SAC"),
 *     @OA\Property(property="address", type="string", example="Av. Los Pinos 123"),
 *     @OA\Property(property="email", type="string", example="mail@mail.com"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="representativeDni", type="string", example="12345678"),
 *     @OA\Property(property="representativeNames", type="string", example="Juan"),
 *     @OA\Property(property="country_id", type="integer", example="1")
 * )
 */
class Person extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'typeDocument',
        'dni',
        'ruc',
        'filterName',
        'names',
        'fatherSurname',
        'motherSurname',
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

    const clientFilters = [
        'ruc' => 'like',
        'filterName' => 'like',
        'email' => 'like',
        'phone' => 'like',
        'representativeDni' => 'like',
        'representativeNames' => 'like',
        'country_id' => '=',
    ];

    const clientSorts = [
        'ruc',
        'businessName',
        'email',
        'phone',
        'representativeDni',
        'representativeNames',
        'country_id',
    ];

    const supplierFilters = [
        'dni' => 'like',
        'ruc' => 'like',
        'filterName' => 'like',
        'email' => 'like',
        'phone' => 'like',
        'country_id' => '=',
    ];

    const supplierSorts = [
        'dni',
        'ruc',
        'filterName',
        'email',
        'phone',
        'country_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'client_id');
    }

    public function purchases()
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }

    public function sales()
    {
        return $this->hasMany(Order::class, 'client_id');
    }


}
