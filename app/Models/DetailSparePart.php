<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailSparePart extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'spare_part_id',
        'quotation_id',
        'order_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'purchasePrice' => 'decimal:2',
        'salePrice' => 'decimal:2',
    ];

    const filters = [
        'quantity' => 'like',
        'movementType' => 'like',
        'purchasePrice' => 'like',
        'salePrice' => 'like',
        'order_id' => 'like',
        'spare_part_id' => 'like',
    ];

    const sorts = [
        'id',
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'order_id',
        'spare_part_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

}
