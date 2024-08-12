<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailMachinery extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'purchaseValue',
        'saleValue',
        'quotation_id',
        'order_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'purchasePrice' => 'decimal:2',
        'salePrice' => 'decimal:2'
    ];

    const filters = [
        'description' => 'like',
        'quantity' => 'like',
        'movementType' => 'like',
        'purchasePrice' => 'like',
        'salePrice' => 'like',
        'purchaseValue' => 'like',
        'saleValue' => 'like',
        'order_id' => 'like',
        'quotation_id' => 'like'
    ];

    const sorts = [
        'id',
        'description',
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'purchaseValue',
        'saleValue',
        'order_id',
        'quotation_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

}
