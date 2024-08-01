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
        'order_id',
        'quotation_id',
        'spare_part_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'quantity' => 'like',
        'movementType' => 'like',
        'purchasePrice' => 'like',
        'salePrice' => 'like',
        'order_id' => 'like',
        'spare_part_id' => 'like',
    ];

}
