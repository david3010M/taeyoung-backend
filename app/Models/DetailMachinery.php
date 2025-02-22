<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailMachinery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_machineries';

    protected $fillable = [
        'description',
        'quantity',
        'movementType',
        'purchasePrice',
        'salePrice',
        'purchaseValue',
        'saleValue',
        'realPrice',
        'contablePrice',
        'machinery_id',
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
        'realPrice'     => 'decimal:2',
        'contablePrice' => 'decimal:2',
    ];

    const filters = [
        'description'   => 'like',
        'quantity'      => 'like',
        'movementType'  => 'like',
        'purchasePrice' => 'like',
        'salePrice'     => 'like',
        'purchaseValue' => 'like',
        'saleValue'     => 'like',
        'realPrice'     => 'like',
        'contablePrice' => 'like',
        'machinery_id'  => 'like',
        'order_id'      => 'like',
        'quotation_id'  => 'like',
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
        'realPrice',
        'contablePrice',
        'machinery_id',
        'order_id',
        'quotation_id',
    ];

    /**
     * Replicar la lógica para actualizar purchasePrice/salePrice en el modelo Machinery.
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            // Si la relación machinery existe y el modelo la trae cargada
            if ($model->machinery) {
                $model->machinery->update([
                    // Si purchasePrice en el detalle es > 0, lo usamos. De lo contrario, se deja el que ya tiene la machinery
                    'purchasePrice' => ($model->purchasePrice && $model->purchasePrice > 0)
                        ? $model->purchasePrice
                        : $model->machinery->purchasePrice,

                    // Igual para salePrice
                    'salePrice'     => ($model->salePrice && $model->salePrice > 0)
                        ? $model->salePrice
                        : $model->machinery->salePrice,
                ]);
            }
        });
    }

    // Relación con la tabla orders (opcional)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relación con la tabla quotations
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    // Relación con la tabla machineries
    public function machinery()
    {
        return $this->belongsTo(Machinery::class);
    }
}
