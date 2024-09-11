<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'date',
        'detail',
        'status',

        'type',
        'documentType',
        'paymentType',
        'currencyType',

        'totalMachinery',
        'totalSpareParts',

        'subtotal',
        'igv',
        'discount',
        'total',
        'totalIncome',
        'totalExpense',
        'balance',

        'comment',
        'supplier_id',
        'quotation_id',
        'client_id',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'totalIncome' => 'decimal:2',
        'totalExpense' => 'decimal:2',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filtersPurchase = [
        'number' => 'like',
        'date' => 'between',
        'supplier_id' => '=',
        'supplier.filterName' => 'like',
        'supplier.country_id' => '=',
    ];

    const filtersSale = [
        'documentType' => '=',
        'number' => 'like',
        'date' => 'between',
        'client_id' => '=',
        'client.filterName' => 'like',
        'client.country_id' => '=',
    ];

    const sortPurchase = [
        'id',
        'number',
        'date',
        'supplier_id',
    ];

    const sortSale = [
        'id',
        'number',
        'date',
        'client_id',
    ];


    public function supplier()
    {
        return $this->belongsTo(Person::class, 'supplier_id');
    }

    public function client()
    {
        return $this->belongsTo(Person::class, 'client_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function detailMachinery()
    {
        return $this->hasMany(DetailMachinery::class);
    }

    public function detailSpareParts()
    {
        return $this->hasMany(DetailSparePart::class);
    }

}
