<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailSparePart extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_spare_parts';

    protected $fillable = [
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'purchaseValue',
        'saleValue',
        'realPrice',       // Nuevo
        'contablePrice',   // Nuevo
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
        'salePrice'     => 'decimal:2',
        'realPrice'     => 'decimal:2',  // Nuevo
        'contablePrice' => 'decimal:2',  // Nuevo
    ];

    const filters = [
        'quantity'      => 'like',
        'movementType'  => 'like',
        'purchasePrice' => 'like',
        'salePrice'     => 'like',
        'purchaseValue' => 'like',
        'saleValue'     => 'like',
        'realPrice'     => 'like',   // Nuevo
        'contablePrice' => 'like',   // Nuevo
        'order_id'      => 'like',
        'spare_part_id' => 'like',
        'quotation_id'  => 'like',   // Si deseas filtrar por quotation_id
    ];

    const sorts = [
        'id',
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'purchaseValue',
        'saleValue',
        'realPrice',       // Nuevo
        'contablePrice',   // Nuevo
        'order_id',
        'spare_part_id',
        'quotation_id',
    ];

    // Si mantienes la lógica para actualizar purchasePrice/salePrice del SparePart
    public static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            if ($model->sparePart) {
                $model->sparePart->update([
                    'purchasePrice' => ($model->purchasePrice && $model->purchasePrice > 0)
                        ? $model->purchasePrice
                        : $model->sparePart->purchasePrice,
                    'salePrice'     => ($model->salePrice && $model->salePrice > 0)
                        ? $model->salePrice
                        : $model->sparePart->salePrice,
                ]);
            }
        });
    }

    // Relación con la tabla orders (opcional, si la usas)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relación con la tabla spare_parts
    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    // Relación con la tabla quotations
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
