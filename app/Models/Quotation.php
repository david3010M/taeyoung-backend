<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Quotation",
 *     required={"date", "currencyType", "price", "initialPayment", "balance", "debts", "exchangeRate", "currency_id", "client_id"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="Q-0001"),
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="detail", type="string", example="This is a detail"),
 *     @OA\Property(property="currencyType", type="string", example="USD"),
 *     @OA\Property(property="paymentType", type="string", enum={"CONTADO", "CREDITO"}, example="CONTADO"),
 *     @OA\Property(property="totalMachinery", type="number", example="100"),
 *     @OA\Property(property="totalSpareParts", type="number", example="100"),
 *     @OA\Property(property="subtotal", type="number", example="100"),
 *     @OA\Property(property="igv", type="number", example="18"),
 *     @OA\Property(property="discount", type="number", example="0"),
 *     @OA\Property(property="total", type="number", example="118"),
 *     @OA\Property(property="client_id", type="integer", example="1")
 * )
 */
class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'date',
        'detail',

        'currencyType',
        'paymentType',
        'totalMachinery',
        'totalSpareParts',
        'subtotal',
        'igv',
        'igvActive',
        'discount',
        'total',
        'client_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'totalMachinery' => 'float',
        'totalSpareParts' => 'float',
        'subtotal' => 'float',
        'igv' => 'float',
        'discount' => 'float',
        'total' => 'float',
        'igvActive' => 'boolean',
    ];

    const filters = [
        'number' => 'like',
        'date' => 'between',
        'description' => 'like',
        'client.filterName' => 'like',
    ];

    const sorts = [
        'id',
        'number',
        'date',
        'client_id',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function client()
    {
        return $this->belongsTo(Person::class);
    }

    public function detailSpareParts()
    {
        return $this->hasMany(DetailSparePart::class)->where('movementType', 'quotation');
    }

    public function detailMachinery()
    {
        return $this->hasMany(DetailMachinery::class)->where('movementType', 'quotation');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

}
