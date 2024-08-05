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
        'type',
        'number',
        'date',
        'documentType',
        'quantity',
        'detail',
        'totalIncome',
        'totalExpense',
        'currency',
        'typePayment',
        'comment',
        'supplier_id',
        'quotation_id',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'totalIncome' => 'decimal:2',
        'totalExpense' => 'decimal:2',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filtersMachineryPurchase = [
        'number' => 'like',
        'date' => 'date',
        'supplier_id' => 'equal',
    ];

    const sortMachineryPurchase = [
        'number',
        'date',
        'supplier_id',
    ];

    const filtersSparePartPurchase = [
        'number' => 'like',
        'date' => 'date',
        'supplier_id' => 'equal',
    ];


    public function supplier()
    {
        return $this->belongsTo(Person::class, 'supplier_id');
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
